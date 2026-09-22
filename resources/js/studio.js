import Quill from 'quill';
import 'quill/dist/quill.snow.css';

document.addEventListener('alpine:init', () => {
    Alpine.data('richTextEditor', (entangled) => ({
        content: entangled,
        quill: null,

        init() {
            const toolbar = this.$refs.toolbar;
            const editor = this.$refs.editor;

            this.quill = new Quill(editor, {
                theme: 'snow',
                modules: {
                    toolbar,
                },
            });

            const setFromWire = (value) => {
                const html = value || '';
                if (this.quill.root.innerHTML !== html) {
                    this.quill.clipboard.dangerouslyPasteHTML(html);
                }
            };

            setFromWire(this.content);

            this.quill.on('text-change', () => {
                const html = this.quill.root.innerHTML;
                const empty = html === '<p><br></p>' || html === '<p></p>';

                this.content = empty ? '' : html;
            });

            this.$watch('content', (value) => setFromWire(value));
        },
    }));
});
