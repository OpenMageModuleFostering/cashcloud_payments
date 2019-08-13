<?php namespace CashCloud\Test\Api\Method;

use CashCloud\Api\Method\GetRates;
use CashCloud\Test\TestCase;

/**
 * Class GetCurrencyRatesTest
 * @package CashCloud\Test\Api\Method
 */
class GetCurrencyRatesTest extends TestCase
{
    public function testIncomplete()
    {
        $request = new GetRates();
        $this->markTestIncomplete('Implement');
    }
}
