<?php

namespace Uspdev\SenhaunicaSocialite\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Uspdev\UspTheme\Events\UspThemeParseKey;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'Uspdev\SenhaunicaSocialite\SenhaunicaExtendSocialite@handle',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Event::listen(function (UspThemeParseKey $event) {
            if ($event->item['key'] == 'senhaunica-socialite') {
                if (session(config('senhaunica.session_key') . '.undo_loginas')) {
                    $event->item = [
                        'text' => '<span class="text-danger"><i class="fas fa-undo"></i> Undo Loginas</span>',
                        'url' => route('SenhaunicaUndoLoginAs'),
                        'can' => 'user',
                    ];
                } else {
                    $event->item = [
                        'text' => '<i class="fas fa-users-cog"></i> Users',
                        'url' => config('senhaunica.userRoutes'),
                        'can' => 'admin',
                    ];
                }
            }
            return $event->item;
        });
    }
}
