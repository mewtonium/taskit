export const titleCase = (str) => {
    return str.replace(/\w+/g, (word) => word.charAt(0).toUpperCase() + word.substr(1).toLowerCase());
}
