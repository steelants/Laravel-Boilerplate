import ace from 'ace-builds';
import 'ace-builds/esm-resolver';

window.ace = ace;

window.aceDefaults = {
    lightTheme: 'chrome',
    darkTheme: 'tomorrow_night',
    options: {
        maxLines: 30,
    },
};

window.loadAce = function($wire = null, language, theme, options = {}){
    return{
        ace: null,
        observer: null,
        init() {
            const editorEl = this.$refs?.editor;
            const textareaEl = this.$refs?.textarea;
            const container = editorEl?.closest?.('.ace-container');

            // options can be passed as argument or as JSON in data-ace-options on the textarea
            options = {
                ...window.aceDefaults.options,
                ...JSON.parse(textareaEl.dataset.aceOptions ?? '{}'),
                ...options,
            };
            if (textareaEl.readOnly || textareaEl.disabled) {
                options.readOnly = true;
            }

            const autoTheme = !theme || theme === 'auto';
            const resolveTheme = () => {
                if (!autoTheme) {
                    return theme;
                }
                const dark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
                return dark ? window.aceDefaults.darkTheme : window.aceDefaults.lightTheme;
            };

            let editor = ace.edit(editorEl, {
                mode: 'ace/mode/'+language,
                theme: 'ace/theme/'+resolveTheme(),
                ...options,
            });

            this.ace = editor;

            if (options.readOnly) {
                editor.setOptions({ highlightActiveLine: false, highlightGutterLine: false });
                editor.renderer.$cursorLayer.element.style.display = 'none';
            }

            if (autoTheme) {
                this.observer = new MutationObserver(() => editor.setTheme('ace/theme/'+resolveTheme()));
                this.observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
            }

            editor.session.setValue(textareaEl.value);

            editor.session.on('change', function(delta) {
                let value = editor.getSession().getValue();
                textareaEl.value = value;
                textareaEl.dispatchEvent(new Event('input'));
            });

            textareaEl.addEventListener('change', function () {
                editor.session.setValue(textareaEl.value, -1);
            });

            if ($wire) {
                $wire.$hook('morphed', function () {
                    if (editor.getValue() !== textareaEl.value) {
                        editor.session.setValue(textareaEl.value);
                    }
                });
            }

            editorEl.classList.add('ready');
            container?.querySelector('.ace-loading')?.remove();
        },
        destroy() {
            this.observer?.disconnect();
            this.ace?.destroy();
        },
    }
}
