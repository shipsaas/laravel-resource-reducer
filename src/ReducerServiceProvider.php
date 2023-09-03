<?php

namespace ShipSaasReducer;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class ReducerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        AboutCommand::add(
            'ShipSaaS: Laravel Resource Reducer',
            fn () => ['Version' => 'v1.0.0']
        );
    }
}
