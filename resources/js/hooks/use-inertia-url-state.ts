import { useCallback, useEffect, useState } from 'react';
import { router, usePage } from '@inertiajs/react';

/**
 * Типы значений, поддерживаемых в URL
 */
type UrlValueType = string | number | boolean | string[] | null | undefined | Record<string, any>;

interface UseInertiaUrlStateOptions {
    /**
     * Автоматически отправлять запрос при изменении параметров
     */
    autoSubmit?: boolean;

    /**
     * Параметры, которые не должны отображаться в URL при значениях по умолчанию
     */
    omitDefaults?: string[];

    /**
     * Не обновлять состояние при изменении URL (например, кнопками браузера)
     */
    ignoreUrlChanges?: boolean;

    /**
     * Дополнительные опции для Inertia router.get
     */
    routerOptions?: {
        preserveState?: boolean;
        preserveScroll?: boolean;
        replace?: boolean;
        only?: string[];
    };
}

export function useInertiaUrlState<T extends Record<string, UrlValueType>>(
    initialParams: T,
    options: UseInertiaUrlStateOptions = {}
) {
    const {
        autoSubmit = true,
        omitDefaults = [],
        ignoreUrlChanges = false,
        routerOptions = { preserveState: true, preserveScroll: true }
    } = options;

    const { props } = usePage();

    /**
     * Чтение вложенных параметров из URL и восстановление структуры
     */
    const getStateFromUrl = useCallback(() => {
        const result = { ...initialParams };
        const urlParams = new URL(window.location.href).searchParams;

        const parseValue = (template: any, raw: string): any => {
            if (typeof template === 'number') return Number(raw);
            if (typeof template === 'boolean') return raw === 'true';
            if (Array.isArray(template)) return raw.split(',');
            return raw;
        };

        const extractNestedParams = (obj: Record<string, any>, prefix = ''): any => {
            const output: Record<string, any> = {};

            Object.entries(obj).forEach(([key, val]) => {
                const fullKey = prefix ? `${prefix}[${key}]` : key;

                if (typeof val === 'object' && val !== null && !Array.isArray(val)) {
                    output[key] = extractNestedParams(val, fullKey);
                } else {
                    const matching = Array.from(urlParams.entries())
                        .filter(([paramKey]) => paramKey === fullKey || paramKey.startsWith(`${fullKey}[`));

                    if (Array.isArray(val)) {
                        const indexedValues = matching
                            .filter(([k]) => /\[\d+\]$/.test(k))
                            .sort((a, b) => {
                                const aIndex = parseInt(a[0].match(/\[(\d+)\]$/)?.[1] ?? '0', 10);
                                const bIndex = parseInt(b[0].match(/\[(\d+)\]$/)?.[1] ?? '0', 10);
                                return aIndex - bIndex;
                            })
                            .map(([_, v]) => v);

                        output[key] = indexedValues.length ? indexedValues : [];
                    } else {
                        const single = urlParams.get(fullKey);
                        if (single !== null) {
                            output[key] = parseValue(val, single);
                        } else {
                            output[key] = val;
                        }
                    }
                }
            });

            return output;
        };

        return extractNestedParams(initialParams);
    }, [initialParams, props]);

    const [params, setParamsInternal] = useState<T>(getStateFromUrl);
    const [isLoading, setIsLoading] = useState<boolean>(false);

    /**
     * Фильтрует параметры, удаляя значения по умолчанию
     */
    const filterDefaultParams = useCallback((newParams: T): Record<string, any> => {
        const filteredParams: Record<string, any> = {};

        Object.entries(newParams).forEach(([key, value]) => {
            if (typeof value === 'object' && !Array.isArray(value)) {
                const nestedFiltered: Record<string, any> = {};
                Object.entries(value as Record<string, any>).forEach(([subKey, subValue]) => {
                    if (
                        subValue !== undefined &&
                        subValue !== null &&
                        subValue !== '' &&
                        !(subValue instanceof Array && subValue.length === 0)
                    ) {
                        nestedFiltered[subKey] = subValue;
                    }
                });

                if (Object.keys(nestedFiltered).length > 0) {
                    filteredParams[key] = nestedFiltered;
                }
            }
            else {
                if (
                    value !== undefined &&
                    value !== null &&
                    value !== '' &&
                    !(omitDefaults.includes(key) && value === initialParams[key as keyof T])
                ) {
                    filteredParams[key] = value;
                }
            }
        });

        return filteredParams;
    }, [initialParams, omitDefaults]);

    /**
     * Обновляет URL в адресной строке браузера
     */
    const updateUrl = useCallback((newParams: T) => {
        const url = new URL(window.location.href);
        url.search = '';
        const searchParams = new URLSearchParams();

        const appendParams = (value: any, key: string) => {
            if (typeof value === 'object' && !Array.isArray(value)) {
                Object.entries(value).forEach(([k, v]) => appendParams(v, `${key}[${k}]`));
            } else if (Array.isArray(value)) {
                value.forEach((v, i) => {
                    if (v !== undefined && v !== null && v !== '') {
                        searchParams.append(`${key}[${i}]`, String(v));
                    }
                });
            } else if (value !== undefined && value !== null && value !== '') {
                searchParams.set(key, String(value));
            }
        };

        Object.entries(newParams).forEach(([key, value]) => {
            appendParams(value, key);
        });

        const queryString = searchParams.toString();
        const newUrl = queryString
            ? `${window.location.pathname}?${queryString}`
            : window.location.pathname;

        window.history.pushState({}, '', newUrl);
    }, [initialParams, omitDefaults]);

    /**
     * Отправляет запрос на сервер через Inertia.js
     */
    const submitRequest = useCallback((newParams: T) => {
        const filteredParams = filterDefaultParams(newParams);
        setIsLoading(true);
        router.get(window.location.pathname, filteredParams, routerOptions);
    }, [routerOptions, filterDefaultParams]);

    /**
     * Устанавливает новые параметры
     */
    const setParams = useCallback(<K extends keyof T>(
        newParamsOrUpdater: Partial<T> | ((prevState: T) => T)
    ) => {
        setParamsInternal(prevParams => {
            const newParams = typeof newParamsOrUpdater === 'function'
                ? newParamsOrUpdater(prevParams)
                : { ...prevParams, ...newParamsOrUpdater };

            updateUrl(newParams);

            if (autoSubmit) {
                submitRequest(newParams);
            }

            return newParams;
        });
    }, [updateUrl, autoSubmit, submitRequest]);

    /**
     * Принудительно отправляет запрос с текущими параметрами
     */
    const submit = useCallback(() => {
        submitRequest(params);
    }, [params, submitRequest]);

    /**
     * Сбрасывает все параметры к начальным значениям
     */
    const reset = useCallback(() => {
        setParamsInternal(initialParams);
        updateUrl(initialParams);
        submitRequest(initialParams);
    }, [initialParams, updateUrl, submitRequest]);

    useEffect(() => {
        const handlePopState = () => {
            if (!ignoreUrlChanges) {
                const newParams = getStateFromUrl();
                setParamsInternal(newParams);

                // При навигации по истории всегда отправляем запрос
                submitRequest(newParams);
            }
        };

        window.addEventListener('popstate', handlePopState);
        return () => {
            window.removeEventListener('popstate', handlePopState);
        };
    }, [submitRequest, getStateFromUrl, ignoreUrlChanges]);

    useEffect(() => {
        const unsubscribeStart = router.on('before', () => {});
        const unsubscribeFinish = router.on('success', () => setIsLoading(false));
        const unsubscribeError = router.on('error', () => setIsLoading(false));

        return () => {
            unsubscribeFinish();
            unsubscribeError();
        };
    }, []);

    return [params, setParams, submit, reset, isLoading] as const;
}

export default useInertiaUrlState;
