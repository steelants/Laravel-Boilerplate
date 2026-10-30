window.loadQuill = function (elementOrConfig, wire = null, mentions = [], tags = []) {
    if (elementOrConfig instanceof HTMLElement) {
        const container = elementOrConfig.closest('.quill-container');
        const textarea = container ? container.querySelector('.quill-textarea') : null;

        if (!textarea) {
            console.warn('loadQuill: textarea element not found.');
            return {};
        }

        // Quill is large, load it only on pages that have an editor
        import('./quill-editor').then(({ createQuillInstance }) => createQuillInstance({
            editorEl: elementOrConfig,
            textareaEl: textarea,
            containerEl: container,
            wire,
            mentions,
            tags,
        }));

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
        async init() {
            const editorEl = this.editorRef || this.$refs?.editor || null;
            const textareaEl = this.textareaRef || this.$refs?.textarea || null;
            const containerEl = this.containerRef || editorEl?.closest?.('.quill-container') || null;

            if (!editorEl || !textareaEl) {
                console.warn('loadQuill: missing editor or textarea reference.');
                return;
            }

            const { createQuillInstance } = await import('./quill-editor');
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
