<?php

namespace ShipSaasReducer\Tests\Features;

use ShipSaasReducer\Tests\App\Models\User;
use ShipSaasReducer\Tests\TestCase;

class UserControllerPaginatedIndexTest extends TestCase
{
    public function testIndexPaginatedReturnsTwoUsersFirstLevel()
    {
        $adminUser = User::where('name', 'Admin')->first();
        $normalUser = User::where('name', 'User')->first();
        $inactivatedUser = User::where('name', 'Inactivated User')->first();

        $res = null;

        // 2 queries: listing & count (because of pagination)
        $this->assertTotalQueries(2, function () use (&$res) {
            $res = $this->json('GET', "/users", [
                'pagination' => 1,
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
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
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
            ->assertJsonMissing([
                'name' => $inactivatedUser->name,
                'email' => $inactivatedUser->email,
            ]);
    }

    public function testIndexPaginatedReturnsTwoUsersSecondLevel()
    {
        $adminUser = User::where('name', 'Admin')->first();
        $normalUser = User::where('name', 'User')->first();
        $inactivatedUser = User::where('name', 'Inactivated User')->first();

        $res = null;
        $this->assertTotalQueries(3, function () use (&$res) {
            $res = $this->json('GET', "/users", [
                'pagination' => 1,
                '_f' => [
                    'name',
                    'role.id',
                    'role.name',
                ],
            ]);
        });

        $res->assertOk()
            ->assertJsonStructure([
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                ],
            ])
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
            ->assertJsonMissing([
                'name' => $inactivatedUser->name,
                'role' => null,
            ]);
    }

    public function testIndexPaginatedReturnsTwoUsersThirdLevel()
    {
        $adminUser = User::where('name', 'Admin')->first();
        $normalUser = User::where('name', 'User')->first();
        $inactivatedUser = User::where('name', 'Inactivated User')->first();

        $res = null;
        $this->assertTotalQueries(5, function () use (&$res) {
            $res = $this->json('GET', "/users", [
                'pagination' => 1,
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
            ->assertJsonStructure([
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                ],
            ])
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
            ->assertJsonMissing([
                'name' => $inactivatedUser->name,
            ]);

        $secondRes = null;

        // 2 queries for user & count
        // 1 for articles
        $this->assertTotalQueries(3, function () use (&$secondRes) {
            $secondRes = $this->json('GET', "/users", [
                'pagination' => 1,
                'page' => 2,
                '_f' => [
                    'name',
                    'role.id',
                    'role.name',
                    'role.permissions.name',
                    'articles.title',
                ],
            ]);
        });

        $secondRes->assertOk()
            ->assertJsonStructure([
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                ],
            ])
            ->assertJsonFragment([
                'name' => $inactivatedUser->name,
                'role' => null,
                'articles' => [],
            ])
            ->assertJsonMissing([
                'name' => $adminUser->name,
            ]);
    }
}
