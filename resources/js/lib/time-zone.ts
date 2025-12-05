export const getUserTimezone = (): string => {
    const offset = -new Date().getTimezoneOffset();
    const hours = Math.floor(Math.abs(offset) / 60);
    const minutes = Math.abs(offset) % 60;

    let tzString = "UTC";
    if (offset !== 0) {
        tzString += offset > 0 ? "+" : "-";
        tzString += hours;
        if (minutes !== 0) {
            tzString += ":" + (minutes < 10 ? "0" + minutes : minutes);
        }
    }

    return tzString;
};
