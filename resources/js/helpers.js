export const titleCase = (str) => {
    return str.replace(/\w+/g, (word) => word.charAt(0).toUpperCase() + word.substr(1).toLowerCase());
}

export const formatDate = (date, format) => {
    const formatted = window.dayjs(date || null).format(format);

    return formatted !== 'Invalid Date' ? formatted : null;
}
