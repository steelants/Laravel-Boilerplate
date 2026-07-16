# Alerts

SteelAnts Laravel-Boilerplate provides a snackbar notification system.

Alerts are dispatched using the `Alert` facade.


## Alert Types

- `success`
- `error`
- `warning`
- `info`

Each type has a default icon.


## Adding Alerts

Add an alert using:

```php
use SteelAnts\LaravelBoilerplate\Facades\Alert;
use SteelAnts\LaravelBoilerplate\Types\AlertModeType;

Alert::add(type: 'success', text: 'Record saved.');
```


## Alert Modes

**RELOAD** - stored in the flash session and shown after a redirect:

```php
Alert::add(type: 'info', text: 'Message shown after redirect.', mode: AlertModeType::RELOAD);
```

**INSTANT** - shown immediately in the same request:

```php
Alert::add(type: 'error', text: 'Message shown in the same request.', mode: AlertModeType::INSTANT);
```


## Parameters

| Parameter | Description |
|---|---|
| `type` | Alert type (`success`, `error`, `warning`, `info`) |
| `text` | Displayed message |
| `icon` | Optional icon; the default icon of the type is used when empty |
| `mode` | `AlertModeType::RELOAD` or `AlertModeType::INSTANT` |
| `persist` | When `true`, the alert stays visible until dismissed by the user or a redirect |


## Rendering

Alerts are rendered by the published alerts component in the application layout.

For the component and the `alert()` helper see:

[Alert component](components/alert.md)


## Next Steps

Continue with:

- [Usage](usage.md)
- [Menu Builder](menu.md)
- [Components](components.md)
