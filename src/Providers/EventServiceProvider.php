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
            if (isset($event->item['key']) && $event->item['key'] == 'senhaunica-socialite') {
                if (session(config('senhaunica.session_key') . '.undo_loginas')) {
                    // está em outra identidade. Vamos mostrar o botão para retornar
                    $event->item = [
                        'text' => '<i class="fas fa-undo text-danger"></i>',
                        'url' => route('SenhaunicaUndoLoginAs'),
                        'title' => 'Undo Loginas',
                        'can' => 'user',
                    ];
                } else {
                    if (config('senhaunica.userRoutes')) {
                        // se ativo a rota users vamos mostrar o botão
                        $itens[] = [
                            'text' => '<i class="fas fa-users-cog text-danger"></i>',
                            'url' => config('senhaunica.userRoutes'),
                            'can' => ($event->item['can'] ?? 'admin'),
                        ];
                    }
                    // mostrando o botão de loginas
                    $itens[] = [
                        'text' => '<i class="fas fa-user-secret text-danger"></i>',
                        'title' => 'Assumir identidade',
                        'url' => route('SenhaunicaLoginAsForm'),
                        'can' => ($event->item['can'] ?? 'admin'),
                    ];
                    $event->item = $itens;
                }
            }
            return $event->item;
        });
    }
}
