window.lumina = {
    toast(message) {
        window.dispatchEvent(new CustomEvent('lumina-toast', { detail: message }));
    },
};
