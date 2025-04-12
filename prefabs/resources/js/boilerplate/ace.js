window.loadAce = function(element, $wire = null, language, theme){
    let container = element.closest('.ace-container');
    let textarea = container.querySelector('.ace-textarea');

    let editor = ace.edit(element, {
        mode: 'ace/mode/'+language,
        theme: 'ace/theme/'+theme,
        maxLines: 30,
    });

    editor.session.setValue(textarea.value);

    editor.session.on('change', function(delta) {
        let value = editor.getSession().getValue();
        textarea.value = value;
        textarea.dispatchEvent(new Event('input'));
    });

    textarea.addEventListener('change', function () {
        editor.session.setValue(textarea.value, -1);
    });

    if ($wire) {
        $wire.hook('morphed', function () {
            editor.session.setValue(textarea.value);
        });
    }

    element.classList.add('ready');
    container.querySelector('.ace-loading').remove();
}
