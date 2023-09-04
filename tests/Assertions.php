<?php

namespace ShipSaasReducer\Tests;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Assert;

/**
 * @mixin TestCase
 */
trait Assertions
{
    protected function withDbQueryLogEnabled(callable $handler): array
    {
        DB::enableQueryLog();

        call_user_func($handler);

        DB::disableQueryLog();

        return DB::getQueryLog();
    }

    /**
     * Assert total queries after handled a task/API call/...
     */
    public function assertTotalQueries(int $expected, callable $handler)
    {
        $logs = $this->withDbQueryLogEnabled($handler);

        Assert::assertCount($expected, $logs);
    }
}
