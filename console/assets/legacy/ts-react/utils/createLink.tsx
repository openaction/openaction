export const createLink = (template: string, params: { [key: string]: string }) => {
    for (let key in params) {
        template = template.replace(key, params[key]);
    }

    return template;
};
