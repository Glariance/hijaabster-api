<?php

namespace App\Providers;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->role_id == config('constants.' . $role);
        });

        // Dynamically apply SMTP settings stored in the database (admin panel)
        try {
            $smtp = SmtpSetting::find(1);
            if ($smtp) {
                Config::set('mail.default', $smtp->mail_driver ?? 'smtp');
                Config::set('mail.mailers.smtp.host', $smtp->mail_host);
                Config::set('mail.mailers.smtp.port', $smtp->mail_port);
                Config::set('mail.mailers.smtp.username', $smtp->mail_username);
                Config::set('mail.mailers.smtp.password', $smtp->mail_password);
                Config::set('mail.mailers.smtp.encryption', $smtp->mail_encryption);
                Config::set('mail.from.address', $smtp->mail_from_address ?? config('mail.from.address'));
                Config::set('mail.from.name', $smtp->mail_from_name ?? config('mail.from.name', config('app.name')));
            }
        } catch (\Throwable $e) {
            // Avoid breaking app boot if table isn't migrated yet
        }
    }
}
