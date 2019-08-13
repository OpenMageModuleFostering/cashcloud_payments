<?php namespace CashCloud\Test\Api\Rest;
use CashCloud\Api\Rest\Auth;
use CashCloud\Api\Rest\CurlRequest;

/**
 * Class AuthTest
 * @package CashCloud\Test\Api\Rest
 */
class AuthTest extends \PHPUnit_Framework_TestCase
{
    public function testHeaderIsSet()
    {
        /** @var \CashCloud\Api\Rest\CurlRequest $request */
        $auth = new Auth("email", "pass", "devId");
        $api = $this->getMock('CashCloud\Api\Rest\Client', ['getSalt'], [$auth]);
        $api->expects($this->any())->method('getSalt')->will($this->returnValue('salt'));
        $request = new CurlRequest();
        $auth->authorizeRequest($api, $request);
        $headers = $request->getHeaders();
        $this->assertArrayHasKey('Authorization', $headers);
        return $request;
    }

    /**
     * @depends testHeaderIsSet
     * @param CurlRequest $request
     */
    public function testAuthorizationToken(CurlRequest $request)
    {
        $hash = "Token ZW1haWx8ZGV2SWR8M2E0ZmZhOTE3NWE2OTJjMWZmNWVkZDNkOTAyYzdkMDY=";
        $this->assertEquals($hash, $request->getHeaders('Authorization'));
    }

    public function testDeviceIdIsSetIfLeftEmpty()
    {
        $auth = new Auth("email", "password");
        $deviceId = $auth->getDeviceId();
        $this->assertNotEmpty($deviceId, "If left empty, device ID should be generated");
        $this->assertEquals($deviceId, $auth->getDeviceId(), "Device ID regenerated when asking it second time");
    }
}
