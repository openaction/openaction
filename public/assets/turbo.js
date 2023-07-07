import '@hotwired/turbo';

const animateLinks = '1' === document.body.getAttribute('data-animate-links');

// Enable Turbo only when requested (data-turbo="true" or option enabled)
document.documentElement.addEventListener('turbo:click', (e) => {
    // Forced usage of Turbo
    if ('true' === e.target.getAttribute('data-turbo')) {
        return;
    }

    // Optional usage of Turbo (depending on option)
    if ('optional' === e.target.getAttribute('data-turbo') && animateLinks) {
        return;
    }

    // Otherwise don't use Turbo
    e.preventDefault();
});
