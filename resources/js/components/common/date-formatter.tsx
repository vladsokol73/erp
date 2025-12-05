import React, { useState, useEffect, useRef } from 'react';
import { useTimezoneStore } from '@/store/useTimezoneStore';

// Варианты форматирования даты
export type DateFormatVariant =
    | 'short'        // 15.5.23
    | 'medium'       // May 15, 2023
    | 'relative'     // 3 months ago, yesterday, 5 minutes ago, 1 hour ago
    | 'original'     // 2024-10-03 10:48:04 (без изменений)
    | 'full'         // Monday, May 15, 2023
    | 'shortWithTime' // 15.5.23 12:30:32
    | undefined;     // если не указано, то используем 'shortWithTime'

interface DateFormatterProps {
    dateString: string;
    variant?: DateFormatVariant;
    timeZoneOffset?: number;
    className?: string;
    locale?: string;
    showTimeZone?: boolean;
    // Настройки автообновления (только для варианта 'relative')
    autoRefresh?: boolean;          // Включить автообновление для относительных дат
    refreshInterval?: number;       // Интервал обновления в секундах
    onRefresh?: (formattedDate: string) => void; // Коллбэк при обновлении
}

export const DateFormatter: React.FC<DateFormatterProps> = ({
                                                                dateString,
                                                                variant,
                                                                timeZoneOffset: propTimeZoneOffset,
                                                                className,
                                                                locale = 'en-US',
                                                                showTimeZone = false,
                                                                // Значения по умолчанию для новых пропсов
                                                                autoRefresh = false,
                                                                refreshInterval = 60, // 1 минута по умолчанию
                                                                onRefresh,
                                                            }) => {
    // Состояние для хранения отформатированной даты
    const [formattedOutput, setFormattedOutput] = useState<string>('');

    const intervalRef = useRef<number | null>(null);

    // Получаем смещение в минутах из хранилища
    const storeTimeZoneOffset = useTimezoneStore().timezoneOffset;
    const timeZoneOffset = propTimeZoneOffset !== undefined ? propTimeZoneOffset : storeTimeZoneOffset;
    const effectiveVariant = variant === undefined ? 'shortWithTime' : variant;

    const formatDate = () => {
        if (!dateString) {
            return '-';
        }

        try {
            let date: Date;

            const isoString = dateString.replace(' ', 'T') + 'Z';
            date = new Date(isoString);


            if (isNaN(date.getTime())) {
                const parts = dateString.split(/[- :.]/);
                if (parts.length >= 6) {
                    date = new Date(
                        Date.UTC(
                            parseInt(parts[0]), // год
                            parseInt(parts[1]) - 1, // месяц (0-11)
                            parseInt(parts[2]), // день
                            parseInt(parts[3]), // часы
                            parseInt(parts[4]), // минуты
                            parseInt(parts[5])  // секунды
                        )
                    );
                } else {
                    return dateString;
                }
            }

            const userOffset = timeZoneOffset;
            const utcTime = date.getTime() + date.getTimezoneOffset() * 60 * 1000;
            const targetTime = new Date(utcTime - userOffset * 60 * 1000);

            let timeZoneInfo = '';
            if (showTimeZone) {
                const absOffset = Math.abs(userOffset);
                const hours = Math.floor(absOffset / 60);
                const minutes = absOffset % 60;

                const sign = userOffset <= 0 ? '+' : '-';

                if (minutes === 0) {
                    timeZoneInfo = `UTC${sign}${hours}`;
                } else {
                    timeZoneInfo = `UTC${sign}${hours}:${minutes.toString().padStart(2, '0')}`;
                }
            }

            let formattedDate = '';
            const formatOptions: Intl.DateTimeFormatOptions = {};

            switch (effectiveVariant) {
                case 'short':
                    // Формат: 15.5.23
                    formatOptions.day = '2-digit';
                    formatOptions.month = '2-digit';
                    formatOptions.year = '2-digit';
                    break;

                case 'medium':
                    // Формат: May 15, 2023
                    formatOptions.day = 'numeric';
                    formatOptions.month = 'long';
                    formatOptions.year = 'numeric';
                    break;

                case 'relative': {
                    const now = new Date();

                    const nowUtc = Date.UTC(
                        now.getUTCFullYear(),
                        now.getUTCMonth(),
                        now.getUTCDate(),
                        now.getUTCHours(),
                        now.getUTCMinutes(),
                        now.getUTCSeconds()
                    );

                    const targetUtc = Date.UTC(
                        date.getUTCFullYear(),
                        date.getUTCMonth(),
                        date.getUTCDate(),
                        date.getUTCHours(),
                        date.getUTCMinutes(),
                        date.getUTCSeconds()
                    );

                    const diffMs = nowUtc - targetUtc;
                    const isFuture = diffMs < 0;

                    const absDiffMs = Math.abs(diffMs);
                    const absDiffSec = Math.floor(absDiffMs / 1000);
                    const absDiffMin = Math.floor(absDiffSec / 60);
                    const absDiffHour = Math.floor(absDiffMin / 60);
                    const absDiffDay = Math.floor(absDiffHour / 24);
                    const absDiffMonth = Math.floor(absDiffDay / 30);
                    const absDiffYear = Math.floor(absDiffMonth / 12);

                    let timeText = '';

                    if (absDiffSec < 60) {
                        timeText = absDiffSec + ' seconds';
                    } else if (absDiffMin < 60) {
                        timeText = absDiffMin + ' minutes';
                    } else if (absDiffHour < 24) {
                        timeText = absDiffHour + ' hours';
                    } else if (absDiffDay < 30) {
                        timeText = absDiffDay + ' days';
                    } else if (absDiffMonth < 12) {
                        timeText = absDiffMonth + ' months';
                    } else {
                        timeText = absDiffYear + ' years';
                    }

                    formattedDate = timeText + (isFuture ? ' from now' : ' ago');
                    break;
                }

                case 'full':
                    // Формат: Monday, May 15, 2023
                    formatOptions.weekday = 'long';
                    formatOptions.day = 'numeric';
                    formatOptions.month = 'long';
                    formatOptions.year = 'numeric';
                    break;

                case 'shortWithTime':
                    // Формат: 15.5.23 12:30:32
                    formatOptions.day = '2-digit';
                    formatOptions.month = '2-digit';
                    formatOptions.year = '2-digit';
                    formatOptions.hour = '2-digit';
                    formatOptions.minute = '2-digit';
                    formatOptions.second = '2-digit';
                    break;

                case 'original':
                    // Формат: 2024-10-03 10:48:04
                    const year = targetTime.getFullYear();
                    const month = (targetTime.getMonth() + 1).toString().padStart(2, '0');
                    const day = targetTime.getDate().toString().padStart(2, '0');
                    const hours = targetTime.getHours().toString().padStart(2, '0');
                    const minutes = targetTime.getMinutes().toString().padStart(2, '0');
                    const seconds = targetTime.getSeconds().toString().padStart(2, '0');
                    formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    break;

                default:
                    // По умолчанию используем полный формат с датой и временем
                    formatOptions.day = 'numeric';
                    formatOptions.month = 'numeric';
                    formatOptions.year = 'numeric';
                    formatOptions.hour = 'numeric';
                    formatOptions.minute = 'numeric';
                    formatOptions.second = 'numeric';
                    break;
            }

            if (Object.keys(formatOptions).length > 0 && !formattedDate) {
                const formatter = new Intl.DateTimeFormat(locale, formatOptions);
                formattedDate = formatter.format(targetTime);
            }


            if (showTimeZone && timeZoneInfo && effectiveVariant !== 'relative') {
                formattedDate += ` (${timeZoneInfo})`;
            }

            return formattedDate;
        } catch (error) {
            console.error('Error formatting date:', error, { dateString, timeZoneOffset });
            return dateString;
        }
    };

    // Функция обновления даты
    const updateFormattedDate = () => {
        const result = formatDate();
        setFormattedOutput(result);

        if (onRefresh) {
            onRefresh(result);
        }
    };

    useEffect(() => {
        updateFormattedDate();

        if (autoRefresh && effectiveVariant === 'relative') {
            if (intervalRef.current !== null) {
                clearInterval(intervalRef.current);
                intervalRef.current = null;
            }

            intervalRef.current = window.setInterval(() => {
                updateFormattedDate();
            }, refreshInterval * 1000);
        }

        return () => {
            if (intervalRef.current !== null) {
                clearInterval(intervalRef.current);
                intervalRef.current = null;
            }
        };
    }, [
        dateString,
        variant,
        timeZoneOffset,
        locale,
        showTimeZone,
        autoRefresh,
        refreshInterval
    ]);

    return <span className={className}>{formattedOutput || '-'}</span>;
};

export default DateFormatter;
