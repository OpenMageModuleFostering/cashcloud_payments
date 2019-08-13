<?php namespace CashCloud\Test\Api\Method;

use CashCloud\Api\Method\SaveSettings;
use CashCloud\Test\TestCase;

/**
 * Class SaveSettingsTest
 * @package CashCloud\Test\Api\Method
 */
class SaveSettingsTest extends TestCase
{
    public function testIncomplete()
    {
        $request = new SaveSettings();
        $this->markTestIncomplete('Implement');
    }
}
