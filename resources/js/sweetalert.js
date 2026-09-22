import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const brand = {
    confirmButtonColor: '#16120f',
    cancelButtonColor: '#9b7b4b',
};

function asList(messages) {
    if (!messages) {
        return [];
    }

    if (Array.isArray(messages)) {
        return messages.flatMap((item) => asList(item));
    }

    if (typeof messages === 'object') {
        return Object.values(messages).flatMap((item) => asList(item));
    }

    return [String(messages)];
}

window.Swal = Swal;

window.notify = {
    success(message, title = 'Success') {
        return Swal.fire({
            icon: 'success',
            title,
            text: message || undefined,
            ...brand,
        });
    },

    error(message, title = 'Error') {
        return Swal.fire({
            icon: 'error',
            title,
            text: message || undefined,
            ...brand,
        });
    },

    warning(message, title = 'Notice') {
        return Swal.fire({
            icon: 'warning',
            title,
            text: message || undefined,
            ...brand,
        });
    },

    info(message, title = 'Info') {
        return Swal.fire({
            icon: 'info',
            title,
            text: message || undefined,
            ...brand,
        });
    },

    validation(errors, title = 'Please fix the following') {
        const list = asList(errors).filter(Boolean);

        if (!list.length) {
            return Promise.resolve();
        }

        const html = `<ul style="text-align:left;margin:0;padding-left:1.1rem;line-height:1.5">${
            list.map((item) => `<li>${item}</li>`).join('')
        }</ul>`;

        return Swal.fire({
            icon: 'error',
            title,
            html,
            ...brand,
        });
    },

    toast(message, icon = 'success') {
        return Swal.fire({
            toast: true,
            position: 'top-end',
            icon,
            title: message,
            showConfirmButton: false,
            timer: 3200,
            timerProgressBar: true,
        });
    },

    confirm(message = 'Are you sure?', title = 'Confirm') {
        return Swal.fire({
            icon: 'warning',
            title,
            text: message,
            showCancelButton: true,
            confirmButtonText: 'Yes, continue',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            ...brand,
        }).then((result) => result.isConfirmed);
    },
};

// Keep legacy helper working.
window.lumina = {
    toast(message) {
        window.notify.toast(message);
    },
};

function showFlashFromPage() {
    const el = document.getElementById('swal-flash');
    if (!el) {
        return;
    }

    const type = el.dataset.type || 'success';
    const message = el.dataset.message || '';
    if (!message) {
        return;
    }

    if (type === 'error') {
        window.notify.error(message);
    } else if (type === 'warning') {
        window.notify.warning(message);
    } else if (type === 'info') {
        window.notify.info(message);
    } else {
        window.notify.success(message);
    }

    el.remove();
}

function showBladeValidationErrors() {
    const el = document.getElementById('swal-errors');
    if (!el) {
        return;
    }

    try {
        const errors = JSON.parse(el.textContent || '[]');
        if (Array.isArray(errors) && errors.length) {
            window.notify.validation(errors);
        }
    } catch (e) {
        // ignore
    }

    el.remove();
}

document.addEventListener('DOMContentLoaded', () => {
    showFlashFromPage();
    showBladeValidationErrors();
});

document.addEventListener('livewire:navigated', () => {
    showFlashFromPage();
    showBladeValidationErrors();
});

document.addEventListener('livewire:init', () => {
    const patchConfirms = (root = document) => {
        root.querySelectorAll('[wire\\:confirm]').forEach((el) => {
            const message = (el.getAttribute('wire:confirm') || 'Are you sure?').replaceAll('\\n', '\n');

            el.__livewire_confirm = (action, instead) => {
                window.notify.confirm(message).then((ok) => {
                    if (ok) {
                        action();
                    } else {
                        instead();
                    }
                });
            };
        });
    };

    queueMicrotask(() => patchConfirms());

    Livewire.hook('morph.updated', ({ el }) => {
        patchConfirms(el);
    });

    // Allow PHP/Livewire to dispatch: $this->dispatch('notify', type: 'success', message: '...')
    Livewire.on('notify', (payload = {}) => {
        const data = Array.isArray(payload) ? (payload[0] || {}) : payload;
        const type = data.type || 'success';
        const message = data.message || data.text || '';

        if (!message) {
            return;
        }

        if (type === 'error') {
            window.notify.error(message);
        } else if (type === 'warning') {
            window.notify.warning(message);
        } else if (type === 'info') {
            window.notify.info(message);
        } else if (type === 'toast') {
            window.notify.toast(message);
        } else {
            window.notify.success(message);
        }
    });
});
