<?php

namespace ShipSaasReducer\Tests\App;

use Illuminate\Support\ServiceProvider;

class ReducerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/reducer_routes.php');
    }
}
