<?php

namespace ShowHeroes\PassportOAuthProvider;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;

/**
 * Class PassportOAuthServiceProvider
 * @package ShowHeroes\PassportOAuthProvider
 */
class PassportOAuthServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $socialite = $this->app->make(Factory::class);
        $socialite->extend(
            PassportOAuthClientProvider::SOCIALITE_PROVIDER_NAME,
            function ($app) use ($socialite) {
                $config = $app['config']['services.passport'];
                return $socialite->buildProvider(PassportOAuthClientProvider::class, $config);
            }
        );
    }
}
