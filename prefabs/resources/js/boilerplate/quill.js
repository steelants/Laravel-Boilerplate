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

const toolbarOptions = [
    [{ header: [1, 2, 3, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    ['blockquote', 'code-block'],
    ['link', 'image'],
    [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'list': 'check' }],
    ['table'],
    ['clean'],
];

function buildMentionSource(mentions = [], tags = []) {
    const normalizeForSearch = (value) => {
        if (!value) {
            return '';
        }

        return value
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();
    };

    return function (searchTerm, renderList, mentionChar) {
        const values = mentionChar === '@' ? mentions : tags;

        if (searchTerm.length === 0) {
            renderList(values, searchTerm);
            return;
        }

        const normalizedSearchTerm = normalizeForSearch(searchTerm);
        const matches = [];
        for (let i = 0; i < values.length; i++) {
            if (~normalizeForSearch(values[i].value).indexOf(normalizedSearchTerm)) {
                matches.push(values[i]);
            }
        }

        renderList(matches, searchTerm);
    };
}

function createQuillInstance({
    editorEl,
    textareaEl,
    containerEl = null,
    wire = null,
    mentions = [],
    tags = [],
}) {
    const quill = new Quill(editorEl, {
        theme: 'snow',
        readOnly: true,
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
                mentionDenotationChars: ['@', '#'],
                source: buildMentionSource(mentions, tags),
            },
            magicUrl: true,
        }
    });

    quill.clipboard.addMatcher(Node.ELEMENT_NODE, (node, delta) => {
        if (node instanceof HTMLElement) {
            node.style.removeProperty('color');
            node.style.removeProperty('background');
            node.style.removeProperty('background-color');
            node.removeAttribute('color');
            node.removeAttribute('bgcolor');
        }

        for (let i = 0; i < delta.ops.length; i++) {
            const op = delta.ops[i];
            if (!op.attributes) {
                continue;
            }

            delete op.attributes.color;
            delete op.attributes.background;
            if (Object.keys(op.attributes).length === 0) {
                delete op.attributes;
            }
        }

        return delta;
    });

    if(textareaEl.value){
        quill.clipboard.dangerouslyPasteHTML(textareaEl.value);
    }

    quill.on('text-change', function () {
        const value = quill.root.innerHTML;
        textareaEl.value = value;
        textareaEl.dispatchEvent(new Event('input'));
    });

    textareaEl.addEventListener('change', function () {
        quill.clipboard.dangerouslyPasteHTML(textareaEl.value);
    });

    if (wire && typeof wire.hook === 'function') {
        wire.hook('morphed', function () {
            quill.clipboard.dangerouslyPasteHTML(textareaEl.value);
        });
    }

    editorEl.classList.add('ready');

    quill.enable();

    if (containerEl) {
        const loading = containerEl.querySelector('.quill-loading');
        if (loading) {
            loading.remove();
        }
    }

    return quill;
}

window.loadQuill = function (elementOrConfig, wire = null, mentions = [], tags = []) {
    if (elementOrConfig instanceof HTMLElement) {
        const container = elementOrConfig.closest('.quill-container');
        const textarea = container ? container.querySelector('.quill-textarea') : null;

        if (!textarea) {
            console.warn('loadQuill: textarea element not found.');
            return {};
        }

        createQuillInstance({
            editorEl: elementOrConfig,
            textareaEl: textarea,
            containerEl: container,
            wire,
            mentions,
            tags,
        });

        return {};
    }

    const config = elementOrConfig || {};

    return {
        quill: null,
        ready: false,
        wire: config.wire ?? wire ?? null,
        mentions: config.mentions ?? mentions,
        tags: config.tags ?? tags,
        editorRef: config.editor ?? null,
        textareaRef: config.textarea ?? null,
        containerRef: config.container ?? null,
        init() {
            const editorEl = this.editorRef || this.$refs?.editor || null;
            const textareaEl = this.textareaRef || this.$refs?.textarea || null;
            const containerEl = this.containerRef || editorEl?.closest?.('.quill-container') || null;

            if (!editorEl || !textareaEl) {
                console.warn('loadQuill: missing editor or textarea reference.');
                return;
            }

            this.quill = createQuillInstance({
                editorEl,
                textareaEl,
                containerEl,
                wire: this.wire,
                mentions: this.mentions,
                tags: this.tags,
            });

            this.ready = true;
        },
        refresh() {
            if (!this.quill) {
                return;
            }

            const textareaEl = this.textareaRef || this.$refs?.textarea || null;
            if (textareaEl) {
                this.quill.clipboard.dangerouslyPasteHTML(textareaEl.value);
            }
        },
        updateMentions(list) {
            this.mentions = Array.isArray(list) ? list : [];
        },
        updateTags(list) {
            this.tags = Array.isArray(list) ? list : [];
        },
    };
}
