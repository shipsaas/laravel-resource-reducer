<?php

namespace ShipSaasReducer\Tests\App;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use ShipSaasReducer\Tests\App\Models\User;

class ReducerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/reducer_routes.php');
    }
}
