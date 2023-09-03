<?php

namespace ShipSaasReducer\Tests\Unit\Helpers;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use ShipSaasReducer\Helpers\RequestHelper;

class RequestHelperTest extends TestCase
{
    public function testGetStructuredRequestedFieldsReturnsDefaultOnNothing()
    {
        $request = new Request();

        $fields = RequestHelper::getStructuredRequestedFields($request, function () {
            return ['hehe'];
        });

        $this->assertEquals(['hehe'], $fields);
    }

    public function testGetStructuredRequestedFieldsReturnsStructuredFieldsString()
    {
        $request = new Request();
        $request->merge([
            '_f' => 'id,name,articles.id',
        ]);

        $fields = RequestHelper::getStructuredRequestedFields($request, function () {
            return ['hehe'];
        });

        $this->assertEquals(['id', 'name', 'articles' => ['id']], $fields);
    }

    public function testGetStructuredRequestedFieldsReturnsStructuredFieldsArray()
    {
        $request = new Request();
        $request->merge([
            '_f' => [
                'id',
                'name',
                'articles.id',
            ],
        ]);

        $fields = RequestHelper::getStructuredRequestedFields($request, function () {
            return ['hehe'];
        });

        $this->assertEquals(['id', 'name', 'articles' => ['id']], $fields);
    }
}
