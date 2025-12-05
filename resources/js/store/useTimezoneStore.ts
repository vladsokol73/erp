import { create } from 'zustand';
import { persist } from 'zustand/middleware';

const getUserTimezoneOffset = (): number => {
    try {
        return new Date().getTimezoneOffset();
    } catch (error) {
        console.error('Error getting user timezone:', error);
        return 0;
    }
};

interface TimezoneState {
    // Текущий часовой пояс в минутах (смещение от UTC)
    timezoneOffset: number;

    // Дата и время последнего обновления часового пояса
    lastUpdated: string | null;

    // Установить часовой пояс вручную по минутному смещению
    setTimezoneOffset: (offset: number) => void;

    // Сброс к часовому поясу системы
    resetToSystemTimezone: () => void;
}

export const useTimezoneStore = create<TimezoneState>()(
    persist(
        (set, get) => ({
            // По умолчанию берем часовой пояс системы в минутах
            timezoneOffset: getUserTimezoneOffset(),
            lastUpdated: null,

            // Устанавливаем новый часовой пояс
            setTimezoneOffset: (offset: number) => set({
                timezoneOffset: offset,
                lastUpdated: new Date().toISOString()
            }),

            // Сбрасываем на часовой пояс системы
            resetToSystemTimezone: () => set({
                timezoneOffset: getUserTimezoneOffset(),
                lastUpdated: new Date().toISOString()
            })
        }),
        {
            name: 'user-timezone',
            partialize: (state) => ({
                timezoneOffset: state.timezoneOffset,
                lastUpdated: state.lastUpdated
            })
        }
    )
);
