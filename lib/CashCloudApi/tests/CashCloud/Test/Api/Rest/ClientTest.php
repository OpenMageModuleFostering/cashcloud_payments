<?php
/**
 * File ClientTest.php
 *
 * @version GIT $Id$
 * @package CashCloud\Test\Api\Rest
 */
namespace CashCloud\Test\Api\Rest;
use CashCloud\Api\Rest\Auth;
use CashCloud\Api\Rest\Client;
use CashCloud\Api\Rest\CurlRequest;
use CashCloud\Test\TestCase;

/**
 * Class ClientTest
 * @package CashCloud\Test\Api\Rest
 */
class ClientTest extends TestCase
{
    /**
     * @expectedException \CashCloud\Api\Exception\AuthException
     */
    public function testGetSaltThrowsExceptionOnBadResponseCode()
    {
        $client = new Client(new Auth("email", "pass"));
        $client->getSalt($this->mockRequest(403, ""));
    }

    /**
     * @expectedException \CashCloud\Api\Exception\AuthException
     */
    public function testGetSaltThrowsExceptionOnMallformedJSON()
    {
        $client = new Client(new Auth("email", "pass"));
        $client->getSalt($this->mockRequest(200, "xxx"));
    }

    /**
     * @expectedException \CashCloud\Api\Exception\AuthException
     */
    public function testGetSaltThrowsExceptionOnIncorrectJSON()
    {
        $client = new Client(new Auth("email", "pass"));
        $client->getSalt($this->mockRequest(200, '{"error":false, "nosalt": "xxxx"}'));
    }

    public function testGetSaltOk()
    {
        $client = new Client(new Auth("email", "pass"));
        $salt = $client->getSalt($this->mockRequest(200, '{"error":false, "salt": "xxxx"}'));
        $this->assertEquals('xxxx', $salt);
    }

    public function testRequestIsAuthorized()
    {
        $client = new Client(new Auth("email", "pass"), "salt");
        $request = new CurlRequest();
        $client->authorizeRequest($request);
        $this->assertArrayHasKey("Authorization", $request->getHeaders());
    }
}
