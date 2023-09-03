<?php

namespace ShipSaasReducer\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ShipSaasReducer\Tests\App\ReducerServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;
    use DatabaseTransactions;

    protected function getPackageProviders($app)
    {
        return [
            ReducerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // setup configs
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set("database.connections.sqlite", [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
        ]);

        $app['db']
            ->connection('sqlite')
            ->getSchemaBuilder()
            ->dropAllTables();

        // 1 user - 1 role
        // 1 role - n permissions
        // 1 user - n articles

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name');
            $table->foreignId('role_id')->nullable();

            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');

            $table->string('title');
            $table->string('content');

            $table->timestamps();
        });
    }
}
