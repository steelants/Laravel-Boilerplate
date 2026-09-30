window.bsTooltip = function () {
    return {
        tooltip: null,
        init() {
            this.tooltip = bootstrap.Tooltip.getOrCreateInstance(this.$el);
        },
        destroy() {
            this.tooltip?.dispose();
        },
    }
}

window.copyToClipboard = function (value) {
    return {
        copied: false,
        timeout: null,
        async copy() {
            try {
                await navigator.clipboard.writeText(value);
            } catch (e) {
                const textarea = document.createElement('textarea');
                textarea.value = value;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                textarea.remove();
            }

            this.copied = true;
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => this.copied = false, 2000);
        },
    }
}
