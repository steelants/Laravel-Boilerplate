import Quill from 'quill';
window.Quill = Quill;

import * as quillTableUI from 'quill-table-ui'
import MagicUrl from 'quill-magic-url'
import { Mention, MentionBlot } from "quill-mention";

Quill.register({
    'modules/tableUI': quillTableUI.default
}, true);

Quill.register({ "blots/mention": MentionBlot, "modules/mention": Mention });
Quill.register('modules/magicUrl', MagicUrl);

// Quill.register(Quill.import('attributors/attribute/direction'), true);
// Quill.register(Quill.import('attributors/class/align'), true);
// Quill.register(Quill.import('attributors/class/background'), true);
// Quill.register(Quill.import('attributors/class/color'), true);
// Quill.register(Quill.import('attributors/class/direction'), true);
// Quill.register(Quill.import('attributors/class/font'), true);
// Quill.register(Quill.import('attributors/class/size'), true);
Quill.register(Quill.import('attributors/style/align'), true);
Quill.register(Quill.import('attributors/style/background'), true);
Quill.register(Quill.import('attributors/style/color'), true);
Quill.register(Quill.import('attributors/style/direction'), true);
Quill.register(Quill.import('attributors/style/font'), true);
Quill.register(Quill.import('attributors/style/size'), true);

window.loadQuill = function (element, $wire = null, mentions = [], tags = []) {
    let container = element.closest('.quill-container');
    let textarea = container.querySelector('.quill-textarea');

    const toolbarOptions = [
        [{ header: [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        ['link', 'image'],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'list': 'check' }],
        ['table'],
        ['clean'],
    ];

    let quill = new Quill(element, {
        theme: 'snow',
        modules: {
            table: true,
            tableUI: true,
            toolbar: {
                container: toolbarOptions,
                handlers: {
                    table: function () {
                        this.quill.getModule('table').insertTable(3, 3);
                    }
                }
            },
            clipboard: {
                matchVisual: false
            },
            mention: {
                allowedChars: /^[A-Za-z\sÅÄÖåäö]*$/,
                mentionDenotationChars: ["@", "#"],
                source: function (searchTerm, renderList, mentionChar) {
                    let values;

                    if (mentionChar === "@") {
                        values = mentions;
                    } else {
                        values = tags;
                    }

                    if (searchTerm.length === 0) {
                        renderList(values, searchTerm);
                    } else {
                        const matches = [];
                        for (let i = 0; i < values.length; i++) {
                            if (~values[i].value.toLowerCase().indexOf(searchTerm.toLowerCase())) {
                                matches.push(values[i]);
                            }
                        }
                        renderList(matches, searchTerm);
                    }
                }
            },
            magicUrl: true,
        }
    });

    // let table = quill.getModule('table')
    // container.querySelector('.ql-insert-table').addEventListener('click', function(){
    //     console.log('click');
    //     table.insertTable(2, 2);
    // });

    quill.root.innerHTML = textarea.value;

    quill.on('text-change', function (delta, oldDelta, source) {
        let value = quill.root.innerHTML;
        textarea.value = value;
        textarea.dispatchEvent(new Event('input'));
    });

    textarea.addEventListener('change', function () {
        quill.root.innerHTML = textarea.value;
    });

    if ($wire) {
        $wire.hook('morphed', function () {
            quill.root.innerHTML = textarea.value;
        });
    }

    element.classList.add('ready');
    container.querySelector('.quill-loading').remove();
}
