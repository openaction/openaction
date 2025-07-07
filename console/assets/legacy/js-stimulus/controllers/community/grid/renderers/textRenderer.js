export function textRenderer(params) {
    if (params.value === undefined) {
        return '<div class="community-contact-loader"><i class="fal fa-circle-notch fa-spin"></i></div>';
    }

    const value = params.value ? params.value : '';

    return '<div class="community-contact-value">' + value + '</div>';
}
