import './sweetalert.js';
import './home-hero-slider.js';
import './lead-popup.js';

window.lumina = window.lumina || {
    toast(message) {
        window.notify?.toast(message);
    },
};
