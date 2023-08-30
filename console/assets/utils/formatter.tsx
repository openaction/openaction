export const numberFormat = (number: number) => {
    const numberFormatter = new Intl.NumberFormat(window.Citipo.locale);

    return numberFormatter.format(number);
};

export const dateFormat = (date: string) => {
    return new Date(date).toLocaleDateString(window.Citipo.locale);
};

export const datetimeFormat = (date: string) => {
    return new Date(date).toLocaleString(window.Citipo.locale);
};

export const countryName = (code: string) => {
    const displayNameFormatter = new Intl.DisplayNames(window.Citipo.locale, { type: 'region' });

    return displayNameFormatter.of(code);
};
