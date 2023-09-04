<?php

namespace ShipSaasReducer\Tests\Features;

use Illuminate\Support\Facades\DB;
use ShipSaasReducer\Tests\App\Models\User;
use ShipSaasReducer\Tests\TestCase;

class UserControllerShowTest extends TestCase
{
    public function testShowReturnsSingleUserFirstLevel()
    {
        $adminUser = User::where('name', 'Admin')->first();

        $this->assertTotalQueries(1, function () use ($adminUser, &$res) {
            $res = $this->json('GET', "/users/{$adminUser->id}", [
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
                    'id',
                    'name',
                    'email',
                ],
            ])
            ->assertJsonFragment([
                'name' => 'Admin',
                'email' => 'admin@shipsaas.tech',
            ]);
    }

    public function testShowReturnsSingleUserSecondLevel()
    {
        $adminUser = User::where('name', 'Admin')->first();

        DB::enableQueryLog();
        $res = $this->json('GET', "/users/{$adminUser->id}", [
            '_fields' => [
                'id',
                'name',
                'email',
                'role.id',
                'role.name',
            ],
        ]);

        DB::disableQueryLog();
        $queryLogs = DB::getQueryLog();

        $this->assertCount(2, $queryLogs);

        $res->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role' => [
                        'id',
                        'name',
                    ],
                ],
            ])
            ->assertJsonFragment([
                'name' => 'Admin',
                'email' => 'admin@shipsaas.tech',
            ])
            ->assertJsonFragment([
                'role' => [
                    'id' => $adminUser->role_id,
                    'name' => 'Admin',
                ],
            ]);
    }

    public function testShowReturnsSingleUserThirdLevel()
    {
        $adminUser = User::where('name', 'Admin')->first();

        DB::enableQueryLog();

        $res = $this->json('GET', "/users/{$adminUser->id}", [
            '_f' => [
                'id',
                'name',
                'email',
                'role.id',
                'role.name',
                'role.permissions.name',
                'articles.id',
                'articles.title',
            ],
        ]);

        DB::disableQueryLog();
        $queryLogs = DB::getQueryLog();

        $this->assertCount(4, $queryLogs);

        $res->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role' => [
                        'id',
                        'name',
                        'permissions' => [
                            '*' => [
                                'name',
                            ],
                        ],
                    ],
                    'articles' => [
                        '*' => [
                            'id',
                            'title',
                        ],
                    ],
                ],
            ])
            // permissions
            ->assertJsonFragment([
                'name' => 'View User',
            ])
            ->assertJsonFragment([
                'name' => 'Delete User',
            ])
            ->assertJsonFragment([
                'name' => 'Create User',
            ])
            // articles
            ->assertJsonFragment([
                'title' => 'The Laravel Resource Reducer',
            ])
            ->assertJsonFragment([
                'title' => 'Please start',
            ]);
    }
}
