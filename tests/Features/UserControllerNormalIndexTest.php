<?php

namespace ShipSaasReducer\Tests\Features;

use Illuminate\Support\Facades\DB;
use ShipSaasReducer\Tests\App\Models\User;
use ShipSaasReducer\Tests\TestCase;

class UserControllerNormalIndexTest extends TestCase
{
    public function testIndexReturnsAllUsersFirstLevel()
    {
        $adminUser = User::where('name', 'Admin')->first();
        $normalUser = User::where('name', 'User')->first();
        $inactivatedUser = User::where('name', 'Inactivated User')->first();

        $res = null;
        $this->assertTotalQueries(1, function () use (&$res) {
            $res = $this->json('GET', "/users", [
                '_f' => [
                    'id',
                    'name',
                    'email',
                ],
            ]);
        });

        $res->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
            ])
            ->assertJsonFragment([
                'name' => $adminUser->name,
                'email' => $adminUser->email,
            ])
            ->assertJsonFragment([
                'name' => $normalUser->name,
                'email' => $normalUser->email,
            ])
            ->assertJsonFragment([
                'name' => $inactivatedUser->name,
                'email' => $inactivatedUser->email,
            ]);
    }

    public function testIndexReturnsAllUsersSecondLevel()
    {
        $adminUser = User::where('name', 'Admin')->first();
        $normalUser = User::where('name', 'User')->first();
        $inactivatedUser = User::where('name', 'Inactivated User')->first();

        $res = null;
        $this->assertTotalQueries(2, function () use (&$res) {
            $res = $this->json('GET', "/users", [
                '_f' => [
                    'name',
                    'role.id',
                    'role.name',
                ],
            ]);
        });

        $res->assertOk()
            ->assertJsonFragment([
                'name' => $adminUser->name,
                'role' => [
                    'id' => $adminUser->role_id,
                    'name' => $adminUser->role->name,
                ],
            ])
            ->assertJsonFragment([
                'name' => $normalUser->name,
                'role' => [
                    'id' => $normalUser->role_id,
                    'name' => $normalUser->role->name,
                ],
            ])
            ->assertJsonFragment([
                'name' => $inactivatedUser->name,
                'role' => null,
            ]);
    }

    public function testIndexReturnsAllUsersThirdLevel()
    {
        $adminUser = User::where('name', 'Admin')->first();
        $normalUser = User::where('name', 'User')->first();
        $inactivatedUser = User::where('name', 'Inactivated User')->first();

        $res = null;
        $this->assertTotalQueries(4, function () use (&$res) {
            $res = $this->json('GET', "/users", [
                '_f' => [
                    'name',
                    'role.id',
                    'role.name',
                    'role.permissions.name',
                    'articles.title',
                ],
            ]);
        });

        $res->assertOk()
            ->assertJsonFragment([
                'name' => $adminUser->name,
                'role' => [
                    'id' => $adminUser->role_id,
                    'name' => $adminUser->role->name,
                    'permissions' => [
                        ['name' => 'View User'],
                        ['name' => 'Create User'],
                        ['name' => 'Delete User'],
                    ],
                ],
                'articles' => [
                    ['title' => 'The Laravel Resource Reducer'],
                    ['title' => 'Please start'],
                ],
            ])
            ->assertJsonFragment([
                'name' => $normalUser->name,
                'role' => [
                    'id' => $normalUser->role_id,
                    'name' => $normalUser->role->name,
                    'permissions' => [
                        ['name' => 'View User'],
                    ],
                ],
                'articles' => [],
            ])
            ->assertJsonFragment([
                'name' => $inactivatedUser->name,
                'role' => null,
                'articles' => [],
            ]);
    }
}
