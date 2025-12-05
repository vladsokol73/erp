export const formatTimeWithTimezone = (date: Date, utcOffset: string): string => {
    // Создаем новую дату, чтобы не изменять оригинальную
    const newDate = new Date(date);

    // Получаем смещение UTC из строки типа "UTC+3" или "UTC-5:30"
    let offsetHours = 0;
    let offsetMinutes = 0;

    if (utcOffset !== "UTC") {
        const match = utcOffset.match(/UTC([+-])(\d+)(?::(\d+))?/);
        if (match) {
            offsetHours = parseInt(match[2]);
            offsetMinutes = match[3] ? parseInt(match[3]) : 0;

            if (match[1] === '-') {
                offsetHours = -offsetHours;
                offsetMinutes = -offsetMinutes;
            }
        }
    }

    // Получаем текущее UTC время
    const utcTime = new Date(
        date.getTime() + date.getTimezoneOffset() * 60000
    );

    // Добавляем смещение часового пояса
    const timezoneTime = new Date(
        utcTime.getTime() + (offsetHours * 60 + offsetMinutes) * 60000
    );

    // Форматируем время
    const hours = timezoneTime.getHours().toString().padStart(2, '0');
    const minutes = timezoneTime.getMinutes().toString().padStart(2, '0');

    return `${hours}:${minutes}`;
};

export function formatMinutes(totalMinutes: number): string {
    const days = Math.floor(totalMinutes / 1440);
    const hours = Math.floor((totalMinutes % 1440) / 60);
    const minutes = totalMinutes % 60;

    return days > 0
        ? `${days}d ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`
        : `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
};

