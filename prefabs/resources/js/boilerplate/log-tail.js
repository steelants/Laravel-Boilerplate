window.initLogTail = function(config) {
    const url      = config.url;
    const outputId = config.outputId || 'log-output';
    const toggleId = config.toggleId || 'autoload-toggle';
    const pollMs   = config.pollMs || 2000;

    const output = document.getElementById(outputId);
    const toggle = document.getElementById(toggleId);
    if (!output || !toggle) return;

    let offset = config.offset || 0;

    async function fetchNew() {
        try {
            const resp = await fetch(url + '?offset=' + offset, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!resp.ok) return;
            const data = await resp.json();
            if (data.lines && data.lines.length) {
                output.textContent += data.lines.join('\n') + '\n';
                offset = data.offset;
                if (toggle.checked) {
                    window.scrollTo(0, document.documentElement.scrollHeight);
                }
            }
        } catch (e) { /* network blip — next tick retries */ }
    }

    setInterval(function () {
        if (toggle.checked) fetchNew();
    }, pollMs);
};
