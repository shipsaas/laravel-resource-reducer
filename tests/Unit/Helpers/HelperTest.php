<?php

namespace ShipSaasReducer\Tests\Unit\Helpers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;
use ShipSaasReducer\Helpers\Helper;
use stdClass;

class HelperTest extends TestCase
{
    public function testIsEagerLoadableInstanceReturnsTrue()
    {
        $this->assertTrue(Helper::isEagerLoadableInstance(new class() extends Model {}));
        $this->assertTrue(Helper::isEagerLoadableInstance(new Collection()));
    }

    public function testIsEagerLoadableInstanceReturnsFalse()
    {
        $this->assertFalse(Helper::isEagerLoadableInstance(new stdClass()));
    }

    public function testTransformToNestedStructureReturnsNestedArray()
    {
        $input = [
            'id',
            'name',
            'role.permission.id',
            'role.permission.name',
            'role.name',
            'articles.title',
        ];

        $parsed = Helper::transformToNestedStructure($input);

        $this->assertEquals([
            'id',
            'name',
            'role' => [
                'name',
                'permission' => [
                    'id',
                    'name',
                ],
            ],
            'articles' => [
                'title',
            ],
        ], $parsed);
    }
}
