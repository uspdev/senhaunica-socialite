<?php

namespace Uspdev\SenhaunicaSocialite\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User; // Assuming your User model
use Laravel\Socialite\Contracts\User as SocialiteUser;

class SenhaunicaUsuarioLogado {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $socialiteUser;
    public $provider;
    
    public function __construct(User $user, SocialiteUser $socialiteUser, string $provider) {
        $this->user = $user;
        $this->socialiteUser = $socialiteUser;
        $this->provider = $provider;
    }
    
    /**
     * @return \App\Models\User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * @return \Laravel\Socialite\Contracts\User
     */
    public function getSocialiteUser()
    {
        return $this->socialiteUser;
    }
    
    /**
     * @return \Uspdev\SenhaunicaSocialite\Events\string
     */
    public function getProvider()
    {
        return $this->provider;
    }
}
