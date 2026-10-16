# Ace editor

**Tag:** `<x-form::ace>` (from `steelants/form`, the JS lives in `resources/js/boilerplate/ace.js`)

Ace is loaded from npm (`ace-builds`, added to `package.json` by `install:boilerplate`). Modes and themes are resolved by `ace-builds/esm-resolver`, so Vite splits them into chunks that load only when used.

## Options

Any [Ace option](https://github.com/ajaxorg/ace/wiki/Configuring-Ace) can be passed as JSON in `data-ace-options`. A `readonly` or `disabled` attribute switches the editor to read-only mode (no cursor or active line).

```blade
<x-form::ace wire:model="template" language="php_laravel_blade" data-ace-options='{"minLines": 10, "maxLines": 40}' />

<x-form::ace name="payload" language="json" theme="auto" :value="$json" readonly />
```

## Theme by light / dark mode

With `theme="auto"` the editor follows `data-bs-theme` and switches live when the user toggles dark mode. You can change the themes and default options globally:

```js
// resources/js/app.js
import './boilerplate/boilerplate.js';

window.aceDefaults.lightTheme = 'github';
window.aceDefaults.darkTheme = 'one_dark';
window.aceDefaults.options.maxLines = 50;
```

> [!NOTE]
> `x-form::ace` in `steelants/form` still loads Ace from a CDN through `@assets`. Both copies work, but as long as the CDN script is included it replaces the npm `window.ace` on pages that contain the editor.
