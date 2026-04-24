const CURRENCY_CONFIG = {
    locale:   'ru-RU',
    currency: 'RUB',
    maximumFractionDigits: 0,
};

export function useFormatPrice() {
    const formatPrice = (price) => new Intl.NumberFormat(
        CURRENCY_CONFIG.locale,
        {
            style:                 'currency',
            currency:              CURRENCY_CONFIG.currency,
            maximumFractionDigits: CURRENCY_CONFIG.maximumFractionDigits,
        }
    ).format(price);

    return { formatPrice };
}
