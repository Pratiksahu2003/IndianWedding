document.addEventListener('alpine:init', () => {
    Alpine.data('leadPopup', (config = {}) => ({
        open: false,
        delay: config.delay ?? 2500,
        hideHours: config.hideHours ?? 24,
        storageKey: 'unik_lead_popup_hide_until',
        timer: null,

        boot() {
            if (! this.shouldShow()) {
                return;
            }

            this.timer = window.setTimeout(() => {
                this.open = true;
            }, this.delay);
        },

        shouldShow() {
            try {
                const until = Number(localStorage.getItem(this.storageKey) || 0);

                return ! Number.isFinite(until) || until <= Date.now();
            } catch (e) {
                return true;
            }
        },

        rememberDismiss() {
            try {
                const until = Date.now() + (this.hideHours * 60 * 60 * 1000);
                localStorage.setItem(this.storageKey, String(until));
            } catch (e) {
                // ignore quota / private mode
            }
        },

        dismiss() {
            if (! this.open) {
                return;
            }

            this.rememberDismiss();
            this.open = false;

            if (this.timer) {
                window.clearTimeout(this.timer);
                this.timer = null;
            }
        },
    }));
});
