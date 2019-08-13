<?php namespace CashCloud\Test\Api\Method;

use CashCloud\Api\Method\GetSettings;
use CashCloud\Test\TestCase;

/**
 * Class GetSettingsTest
 * @package CashCloud\Test\Api\Method
 */
class GetSettingsTest extends TestCase
{
    public function testIncomplete()
    {
        $request = new GetSettings();
        $this->markTestIncomplete('Implement');
    }
}
