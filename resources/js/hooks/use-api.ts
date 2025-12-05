import { useState, useCallback, useMemo } from 'react';
import axios, { AxiosRequestConfig, AxiosResponse, AxiosError } from 'axios';

// Базовый интерфейс ответа API
interface ApiResponse<T = any> {
    success: boolean;
    data: T | null;
    message: string | null;
    meta: Record<string, any> | null;
}

// Тип для группы имен запросов (только для функций проверки загрузки)
type RequestNamesArray = string[];

// Интерфейс для опций запроса с расширенной обработкой ошибок
interface RequestOptions<T = any> {
    // Функция, вызываемая при успешном запросе (data может быть null)
    onSuccess?: (data: T | null, message?: string | null, meta?: Record<string, any> | null) => void;

    // Функция, вызываемая при клиентских ошибках (4xx)
    onApiError?: (message?: string | null, statusCode?: number, meta?: Record<string, any> | null) => void;

    // Функция, вызываемая при серверных ошибках (5xx)
    onServerError?: (message?: string | null, statusCode?: number, meta?: Record<string, any> | null) => void;

    // Функция, вызываемая при любых HTTP ошибках (4xx и 5xx)
    onError?: (message?: string | null, statusCode?: number, meta?: Record<string, any> | null) => void;

    // Функция, вызываемая при ошибках клиента (сетевые ошибки, таймауты и т.д.)
    onClientError?: (errorType: string, error?: any) => void;

    // Функция, вызываемая в любом случае после запроса
    onFinally?: () => void;

    // Уникальное имя запроса для отслеживания его состояния загрузки
    requestName?: string;

    // Конфигурация axios
    config?: AxiosRequestConfig;
}

// Типы запросов
type RequestType = 'get' | 'post' | 'put' | 'patch' | 'delete';

// Перечисление типов клиентских ошибок
enum ClientErrorType {
    NETWORK = 'network',
    TIMEOUT = 'timeout',
    CANCELED = 'canceled',
    UNKNOWN = 'unknown'
}

/**
 * Хук для работы с API запросами
 * Предоставляет методы для выполнения HTTP-запросов с расширенной обработкой ошибок
 */
function useApi() {
    // Инициализация начальных состояний в одном месте
    const [loadingStates, setLoadingStates] = useState({
        global: false,
        types: {
            get: false,
            post: false,
            put: false,
            patch: false,
            delete: false
        },
        named: {} as Record<string, boolean>
    });

    // Разделяем состояние на более атомарные части для удобства использования
    const loading = loadingStates.global;
    const loadingState = loadingStates.types;
    const namedRequestsLoading = loadingStates.named;

    const [error, setError] = useState<any>(null);

    /**
     * Устанавливает состояние загрузки для указанного типа запроса
     * @param type - Тип запроса
     * @param isLoading - Состояние загрузки
     * @param requestName - Имя запроса (опционально)
     */
    const setTypeLoading = useCallback((type: RequestType, isLoading: boolean, requestName?: string) => {
        setLoadingStates(prev => {
            // Создаем обновленное состояние именованных запросов
            const updatedNamedLoading = { ...prev.named };

            // Если указано имя запроса, обновляем его состояние
            if (requestName) {
                updatedNamedLoading[requestName] = isLoading;
            }

            // Обновляем состояние типа запроса
            const updatedTypes = {
                ...prev.types,
                [type]: isLoading
            };

            return {
                global: isLoading,
                types: updatedTypes,
                named: updatedNamedLoading
            };
        });
    }, []);

    /**
     * Определяет тип клиентской ошибки
     * @param error - Объект ошибки
     * @returns Тип клиентской ошибки
     */
    const getClientErrorType = useCallback((error: any): ClientErrorType => {
        if (axios.isCancel(error)) {
            return ClientErrorType.CANCELED;
        }

        if (error.code === 'ECONNABORTED') {
            return ClientErrorType.TIMEOUT;
        }

        if (error.message && error.message.includes('Network Error')) {
            return ClientErrorType.NETWORK;
        }

        return ClientErrorType.UNKNOWN;
    }, []);

    /**
     * Выполняет HTTP-запрос с обработкой различных типов ошибок
     * @param method - Тип HTTP-метода
     * @param url - URL для запроса
     * @param data - Данные для отправки
     * @param options - Опции запроса
     * @returns Promise с результатом запроса
     */
    const request = useCallback(async <T = any>(
        method: RequestType,
        url: string,
        data?: any,
        options?: RequestOptions<T>
    ): Promise<ApiResponse<T>> => {
        const requestName = options?.requestName;

        setTypeLoading(method, true, requestName);
        setError(null);

        try {
            // Выполняем запрос
            const response: AxiosResponse<ApiResponse<T>> = await axios({
                method,
                url,
                data: ['post', 'put', 'patch'].includes(method) ? data : undefined,
                params: method === 'get' ? data : undefined,
                ...options?.config
            });

            const { success, data: responseData, message, meta } = response.data;

            if (!success) {
                options?.onApiError?.(message, response.status, meta);
                options?.onError?.(message, response.status, meta);
                throw new Error(message ?? "Request failed");
            }

            if (success) {
                // Если запрос успешен (success: true)
                if (options?.onSuccess) {
                    options.onSuccess(responseData, message, meta);
                }
            } else {
                // Если API вернул ошибку (success: false)
                if (options?.onApiError) {
                    options.onApiError(message, response.status, meta);
                }

                // Вызываем также общий обработчик ошибок
                if (options?.onError) {
                    options.onError(message, response.status, meta);
                }
            }

            setTypeLoading(method, false, requestName);
            return response.data;
        } catch (err) {
            setError(err);

            // Обрабатываем различные типы ошибок
            const error = err as AxiosError<ApiResponse<T>>;

            if (error.response) {
                // Ошибка с ответом от сервера (HTTP ошибка)
                const statusCode = error.response.status;
                const responseData = error.response.data;
                const message = responseData?.message || error.message;
                const meta = responseData?.meta || null;

                // Клиентские ошибки (4xx)
                if (statusCode >= 400 && statusCode < 500) {
                    if (options?.onApiError) {
                        options.onApiError(message, statusCode, meta);
                    }
                }

                // Серверные ошибки (5xx)
                if (statusCode >= 500) {
                    if (options?.onServerError) {
                        options.onServerError(message, statusCode, meta);
                    }
                }

                // Общий обработчик для любых HTTP ошибок
                if (options?.onError) {
                    options.onError(message, statusCode, meta);
                }
            } else {
                // Клиентские ошибки (сетевые проблемы, таймауты и т.д.)
                const clientErrorType = getClientErrorType(error);

                if (options?.onClientError) {
                    options.onClientError(clientErrorType, error);
                }

                // Вызываем также общий обработчик ошибок
                if (options?.onError) {
                    options.onError(error.message, undefined, null);
                }
            }

            setTypeLoading(method, false, requestName);
            throw err;
        } finally {
            // Вызываем коллбэк finally, если он есть
            if (options?.onFinally) {
                options.onFinally();
            }
        }
    }, [setTypeLoading, getClientErrorType, setError]);

    /**
     * Выполняет GET-запрос
     * @param url - URL для запроса
     * @param options - Опции запроса
     * @param params - Параметры запроса
     * @returns Promise с результатом запроса
     */
    const get = useCallback(<T = any>(url: string, options?: RequestOptions<T>, params?: any) =>
        request<T>('get', url, params, options), [request]);

    /**
     * Выполняет POST-запрос
     * @param url - URL для запроса
     * @param data - Данные для отправки
     * @param options - Опции запроса
     * @returns Promise с результатом запроса
     */
    const post = useCallback(<T = any>(url: string, data?: any, options?: RequestOptions<T>) =>
        request<T>('post', url, data, options), [request]);

    /**
     * Выполняет PUT-запрос
     * @param url - URL для запроса
     * @param data - Данные для отправки
     * @param options - Опции запроса
     * @returns Promise с результатом запроса
     */
    const put = useCallback(<T = any>(url: string, data?: any, options?: RequestOptions<T>) =>
        request<T>('put', url, data, options), [request]);

    /**
     * Выполняет PATCH-запрос
     * @param url - URL для запроса
     * @param data - Данные для отправки
     * @param options - Опции запроса
     * @returns Promise с результатом запроса
     */
    const patch = useCallback(<T = any>(url: string, data?: any, options?: RequestOptions<T>) =>
        request<T>('patch', url, data, options), [request]);

    /**
     * Выполняет DELETE-запрос
     * @param url - URL для запроса
     * @param options - Опции запроса
     * @returns Promise с результатом запроса
     */
    const deleteRequest = useCallback(<T = any>(url: string, options?: RequestOptions<T>) =>
        request<T>('delete', url, undefined, options), [request]);

    /**
     * Проверяет, находится ли указанный запрос (или группа запросов) в состоянии загрузки
     * @param requestName - Имя запроса или массив имен запросов
     * @param checkType - Тип проверки: 'some' (хотя бы один загружается) или 'every' (все загружаются)
     * @returns Состояние загрузки запроса или запросов в массиве
     */
    const isRequestLoading = useCallback(
        (requestName: string | string[], checkType: 'some' | 'every' = 'some'): boolean => {
            if (Array.isArray(requestName)) {
                if (checkType === 'some') {
                    // Возвращаем true, если хотя бы один из запросов загружается
                    return requestName.some(name => namedRequestsLoading[name] || false);
                } else {
                    // Возвращаем true, только если все запросы загружаются
                    return requestName.every(name => namedRequestsLoading[name] || false);
                }
            }
            // Если передано одно имя запроса, проверяем его состояние
            return namedRequestsLoading[requestName] || false;
        },
        [namedRequestsLoading]
    );

    // Мемоизируем методы проверки загрузки для предотвращения ненужных рендеров
    const anyLoading = useCallback((requestNames: string[]) =>
        isRequestLoading(requestNames, 'some'), [isRequestLoading]);

    const allLoading = useCallback((requestNames: string[]) =>
        isRequestLoading(requestNames, 'every'), [isRequestLoading]);

    // Мемоизируем возвращаемый объект для предотвращения ненужных рендеров
    const apiInterface = useMemo(() => ({
        loading,
        isLoading: {
            get: loadingState.get,
            post: loadingState.post,
            put: loadingState.put,
            patch: loadingState.patch,
            delete: loadingState.delete,
            request: isRequestLoading,
            any: anyLoading,
            all: allLoading
        },
        error,
        get,
        post,
        put,
        patch,
        delete: deleteRequest,
        errorTypes: ClientErrorType
    }), [
        loading, loadingState, isRequestLoading,
        anyLoading, allLoading, error,
        get, post, put, patch, deleteRequest
    ]);

    return apiInterface;
}

export default useApi;
