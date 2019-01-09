<?php

namespace Uspdev\SenhaunicaSocialite;

use SocialiteProviders\Manager\SocialiteWasCalled;

class SenhaunicaExtendSocialite
{
    /**
     * Execute the provider.
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite(
            'senhaunica',
            __NAMESPACE__.'\Provider',
            __NAMESPACE__.'\Server'
        );
    }
}
