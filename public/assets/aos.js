// Animate on scroll
import AOS from 'aos';
import 'aos/dist/aos.css';

AOS.init({
    startEvent: 'load',
    disableMutationObserver: false,

    // Disable AOS when reduced-motion is requested
    disable: window.matchMedia('(prefers-reduced-motion: reduce)'),
});

document.addEventListener('load', () => {
    AOS.refresh(true);
    setTimeout(() => AOS.refresh(true), 300);
});
