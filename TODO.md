# TODO

## Bootstrap 6 migration (branch `bootstrap-6`)

Bootstrap is pinned to `6.0.0-alpha.1`. Related branches: Laravel-Boilerplate.CMS, Laravel-Boilerplate.Dashboard, Laravel-Form, Livewire-DataTable, Livewire-Form, Livewire-Modal.

### Known visual bugs

- [ ] Welcome page example (`prefabs/resources/views/welcome.blade.php`): the demo navigation is broken, nested project items overflow the sidebar and a horizontal scrollbar shows up.
- [ ] Review the pages not checked yet: Gantt, Dashboard editor, CMS node form, forms with checkboxes and radios, tabs, system pages (logs, jobs, backup, cache).
- [ ] Plain `.btn` without a variant lost the v5 hover/active background that `_variables.scss` set on `:root` (`--bs-btn-hover-bg`, `--bs-btn-active-bg`).
- [ ] `<x-dropdown>` and `<x-dropdown-item>` still render `<ul><li>`, Bootstrap 6 recommends plain `<div>`/`<a class="menu-item">`.

### Follow-ups

- [ ] Lazy loaded Ace (`resources/js/boilerplate/ace.js`) is not verified in a browser, the sandbox has no page with an Ace editor.
- [ ] Livewire-DataTable tests were updated to the new markup but not run (no `vendor/` in the package).
- [ ] Livewire-Form `field` component: offer the password strength meter for the `password` type like `x-form::input strength`.
- [ ] Remaining `@include color-mode(dark)` blocks could use `light-dark()` like the Bootstrap 6 tokens.
- [ ] `$form-label-tokens` are never emitted in 6.0.0-alpha.1, `--label-margin-bottom` is set in `$root-tokens` as a workaround. Drop it once Bootstrap fixes it.
- [ ] Decide whether to keep the Bootstrap 5 breakpoints (`lg` 992px, `2xl` 1400px) or move to the Bootstrap 6 defaults.
- [ ] Ace's lazy chunks exceed the 500 kB warning limit, `vite.config.js` raises `chunkSizeWarningLimit` and ignores the `eval` warning of its CoffeeScript worker. Revisit if Ace gets a smaller resolver.

## Release

- [ ] `dev` contains unreleased changes since 3.1.1 (Font Awesome 7, copy to clipboard fix, installer SCSS/JS fix, Livewire 4 modal fix). Release them together with the Font Awesome upgrade.
