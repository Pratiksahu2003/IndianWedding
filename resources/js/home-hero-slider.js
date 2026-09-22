document.addEventListener('alpine:init', () => {
    Alpine.data('homeHeroSlider', (config = {}) => ({
        active: 0,
        total: config.total ?? 5,
        duration: config.duration ?? 6000,
        playing: true,
        timer: null,

        start() {
            this.$nextTick(() => this.restartProgress());
            this.play();
        },

        play() {
            this.stop();
            if (! this.playing) {
                return;
            }

            this.timer = window.setInterval(() => this.next(), this.duration);
        },

        stop() {
            if (this.timer) {
                window.clearInterval(this.timer);
                this.timer = null;
            }
        },

        pause() {
            this.playing = false;
            this.stop();
            this.freezeProgress();
        },

        resume() {
            this.playing = true;
            this.restartProgress();
            this.play();
        },

        go(index) {
            this.active = index;
            this.restartProgress();
            if (this.playing) {
                this.play();
            }
        },

        next() {
            this.active = (this.active + 1) % this.total;
            this.restartProgress();
        },

        bar(index) {
            return this.$refs['bar' + index] ?? null;
        },

        clearBars() {
            for (let i = 0; i < this.total; i++) {
                const el = this.bar(i);
                if (! el) {
                    continue;
                }
                el.classList.remove('home-hero-progress');
                el.style.transform = '';
                el.style.transition = '';
            }
        },

        freezeProgress() {
            const el = this.bar(this.active);
            if (! el) {
                return;
            }
            const computed = window.getComputedStyle(el).transform;
            el.classList.remove('home-hero-progress');
            el.style.transition = 'none';
            el.style.transform = computed === 'none' ? 'scaleX(1)' : computed;
        },

        restartProgress() {
            this.$nextTick(() => {
                this.clearBars();
                const el = this.bar(this.active);
                if (! el || ! this.playing) {
                    if (el && ! this.playing) {
                        el.style.transform = 'scaleX(1)';
                    }
                    return;
                }

                el.style.transition = 'none';
                el.style.transform = 'scaleX(0)';
                void el.offsetWidth;
                el.style.transition = '';
                el.style.transform = '';
                el.style.setProperty('--home-hero-duration', `${this.duration}ms`);
                el.classList.add('home-hero-progress');
            });
        },
    }));
});
