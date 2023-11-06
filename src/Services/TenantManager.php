<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class TenantManager
{
    private ?Tenant $tenant;

    public function __construct($tenant = null)
    {
        $this->set($tenant);
    }

    public function set($tenant = null)
    {
        $this->tenant = $tenant;
        $this->configureMailer();

        if ($tenant != null) {
            Config::set('app.url', (config('app.https') ? 'https://' : 'http://') . $tenant->domain . config('app.url_base'));
        }
    }

    public function getTenant()
    {
        return $this->tenant;
    }

    private function configureMailer()
    {
        if ($this->tenant == null) {
            $this->clearMailer();
            return;
        }

        $settings = $this->tenant->settings->pluck('value', 'key');
        if (!empty($settings['mail.smtp_host']) && !empty($settings['mail.smtp_username']) && !empty($settings['mail.smtp_password'])) {
            Config::set('mail.mailers.smtp_tenant.host', $settings['mail.smtp_host']);
            Config::set('mail.mailers.smtp_tenant.port', $settings['mail.smtp_port']);
            Config::set('mail.mailers.smtp_tenant.username', $settings['mail.smtp_username']);
            Config::set('mail.mailers.smtp_tenant.password', $settings['mail.smtp_password']);
            Config::set('mail.from_tenant.address', $settings['mail.smtp_username']);
        } else {
            $this->clearMailer();
        }
    }

    private function clearMailer()
    {
        Config::set('mail.mailers.smtp_tenant.host', '');
        Config::set('mail.mailers.smtp_tenant.port', '');
        Config::set('mail.mailers.smtp_tenant.username', '');
        Config::set('mail.mailers.smtp_tenant.password', '');
        Config::set('mail.from_tenant.address', '');
    }

    public function mailer()
    {
        if (
            empty(config('mail.mailers.smtp_tenant.host'))
            || empty(config('mail.mailers.smtp_tenant.port'))
            || empty(config('mail.mailers.smtp_tenant.username'))
            || empty(config('mail.mailers.smtp_tenant.password'))
        ) {
            return Mail::mailer('smtp');
        }

        return Mail::mailer('smtp_tenant');
    }
}
