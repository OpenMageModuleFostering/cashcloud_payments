<?php
/**
 * File TestCase.php
 *
 * @version GIT $Id$
 * @package CashCloud\Test
 */
namespace CashCloud\Test;
use CashCloud\Api\Rest\Auth;
use CashCloud\Api\Rest\Client;

/**
 * Class TestCase
 * @package CashCloud\Test
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param integer $responseCode
     * @param string $responseBody
     * @return \PHPUnit_Framework_MockObject_MockObject|\CashCloud\Api\Rest\CurlRequest
     */
    protected function mockRequest($responseCode, $responseBody)
    {
        $mock = $this->getMock('CashCloud\Api\Rest\CurlRequest', ['getResponseCode', 'getBody']);
        $mock->expects($this->any())->method('getResponseCode')->will($this->returnValue($responseCode));
        $mock->expects($this->any())->method('getBody')->will($this->returnValue($responseBody));
        return $mock;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return new Client(new Auth("pass", "user"), "salt");
    }
}
