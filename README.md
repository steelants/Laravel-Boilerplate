# Laravel-Boilerplate

## Currently WIP

### Created by: [SteelAnts s.r.o.](https://www.steelants.cz/)

[![Total Downloads](https://img.shields.io/packagist/dt/steelants/form.svg?style=flat-square)](https://packagist.org/packages/steelants/laravel-boilerplate)

## Content
- User Managment
- Log Viewer
- Audit

## Install

```bash
composer require steelants/laravel-boilerplate
compoer install
php artisan boilerplate:install
```

## Development

### Creation of symlinks for dev environment:

```bash
ln -s ./package/boilerplate/stubs/resources/ resources
```

## Contributors
<a href="https://github.com/steelants/Laravel-Boilerplate/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=steelants/Laravel-Boilerplate" />
</a>

## Other Packages
[steelants/datatable](https://github.com/steelants/Livewire-DataTable)

[steelants/form](https://github.com/steelants/Laravel-Form)

[steelants/modal](https://github.com/steelants/Livewire-Modal)


## Notes
* [Laravel MFA](https://dev.to/roxie/how-to-add-google-s-two-factor-authentication-to-a-laravel-8-application-4jjp)


## TenantManager

### Install

Add `'url_base' => env('APP_URL', 'http://localhost'),` into `config/app.php`

To use globaly tenant manager as `tenant()` add `app/helpers.php` into `autoload.files` config in `composer.json`
```json
{
    ...
    "autoload": {
        ...
        "files": [
            "app/helpers.php"
        ]
    },
    ...
}
```

If your tenants have thier own SMTP settings, add following into `mailers` array in `config/mail.php`
```
'smtp_tenant' => [
    'transport' => 'smtp',
    'host' => '',
    'port' => env('MAIL_PORT', 587),
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    'username' => '',
    'password' => '',
    'timeout' => null,
    'auth_mode' => null,
],
```

### Usage
```php
// Access tenant manager
tenantManager()

// Access current tenant object
tenant()

// is helper wrapper function for
app(TenantManager::class)->getTenant();
```

### Sending emails
```php
// sending emial from tenants own SMTP server
tenantManager()->mailer()->to(...)->send(...);

// for sending emails from app's SMTP server use Laravel's default Mail class
Mail::to(...)->send(...);
```

### Running in console
By default, on web, tenant is set in `TenantServiceProvider` by subdomain. To use `tenant()` or `tenantManager()->mailer()` in console,
for example in jobs, cron, ... you need to manualy set current tenant. 
```php
// Find your tenant
$tenant = Tenant::find($tenantId);

// Set as current tenant
tenantManager()->set($tenant);
```