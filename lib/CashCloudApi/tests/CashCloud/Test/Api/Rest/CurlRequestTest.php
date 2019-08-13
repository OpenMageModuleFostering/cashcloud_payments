<?php namespace CashCloud\Test\Api\Rest;

use CashCloud\Api\Rest\CurlRequest;

/**
 * Class CurlRequest
 * @package CashCloud\Test\Api\Rest
 */
class CurlRequestTest extends \CashCloud\Test\TestCase
{
    public function testHeadersFormat()
    {
        $request = new CurlRequest();
        $request->setHeader("Test", "*");
        $this->assertEquals(['Test:*'], $request->getFormattedHeaders());
    }

    /**
     * @expectedException \CashCloud\Api\Exception\CashCloudException
     */
    public function testEmptyUrl()
    {
        $request = new CurlRequest();
        $request->execute();
    }

    public function testEmptyResponse()
    {
        $request = new CurlRequest();
        $this->assertNull($request->getBody());
        $this->assertNull($request->getResponseCode());
    }

    public function testDoNotExecuteSecondTime()
    {
        $request = $this->mockRequest(array('getResponseCode'));
        $request->expects($this->any())->method('getResponseCode')->will($this->returnValue(200));
        $this->assertEquals(200, $request->execute(), 'Mocking failed?');
        $this->assertEquals(200, $request->execute(), 'Should not execute request');
    }

    /**
     * @param array $methods
     * @return CurlRequest|\PHPUnit_Framework_MockObject_MockObject
     */
    public function mockRequest($methods = array())
    {
        return $this->getMock('CashCloud\Api\Rest\CurlRequest', $methods);
    }
}
