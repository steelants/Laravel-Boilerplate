window.loadAce = function($wire = null, language, theme){
    return{
        ace: null,
        init() {
            const editorEl = this.$refs?.editor;
            const textareaEl = this.$refs?.textarea;
            const container = editorEl?.closest?.('.ace-container');

            let editor = ace.edit(editorEl, {
                mode: 'ace/mode/'+language,
                theme: 'ace/theme/'+theme,
                maxLines: 30,
            });

            this.ace = editor;

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
                $wire.hook('morphed', function () {
                    editor.session.setValue(textareaEl.value);
                });
            }

            editorEl.classList.add('ready');
            container.querySelector('.ace-loading').remove();
        }
    }
}
