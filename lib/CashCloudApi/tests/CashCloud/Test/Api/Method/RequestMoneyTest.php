<?php namespace CashCloud\Test\Api\Method;

use CashCloud\Api\Method\RequestMoney;
use CashCloud\Test\TestCase;

/**
 * Class RequestMoneyTest
 * @package CashCloud\Method
 */
class RequestMoneyTest extends TestCase
{
    /**
     * @expectedException \CashCloud\Api\Exception\CashCloudException
     * @expectedExceptionMessage Unknown exception
     */
    public function testUnknownCode()
    {
        $request = new RequestMoney($this->mockRequest(800, ""));
        $request->setEmail("email");
        $request->setCurrency("EUR");
        $request->setAmount(10);
        $request->perform($this->getClient());
    }

    /**
     * @expectedException \CashCloud\Api\Exception\CashCloudException
     * @expectedExceptionMessage Invalid url
     */
    public function test404Error()
    {
        $request = new RequestMoney($this->mockRequest(404, ""));
        $request->setEmail("email");
        $request->setCurrency("EUR");
        $request->setAmount(10);
        $request->perform($this->getClient());
    }

    /**
     * @expectedException \CashCloud\Api\Exception\AuthException
     */
    public function testAuthError()
    {
        $request = new RequestMoney($this->mockRequest(403, ""));
        $request->setEmail("email");
        $request->setCurrency("EUR");
        $request->setAmount(10);
        $request->perform($this->getClient());
    }

    /**
     * @expectedException \CashCloud\Api\Exception\CashCloudException
     */
    public function testServerError()
    {
        $request = new RequestMoney($this->mockRequest(500, ""));
        $request->setEmail("email");
        $request->setCurrency("EUR");
        $request->setAmount(10);
        $request->perform($this->getClient());
    }

    /**
     * @expectedException \CashCloud\Api\Exception\ValidateException
     * @expectedExceptionMessage SendMoneyOptionBlocked
     */
    public function testValidateError()
    {
        $request = new RequestMoney($this->mockRequest(412, json_encode(array(
            'validationFailed'=>array(
                "SendMoneyOptionBlocked",
                "minAmountTransactionPerTradeLimit",
            )
        ))));
        $request->setEmail("email");
        $request->setCurrency("EUR");
        $request->setAmount(10);
        $request->perform($this->getClient());
        $this->markTestIncomplete("validation messages is not covered");
    }

    /**
     * @expectedException \CashCloud\Api\Exception\CashCloudException
     */
    public function testInvalidJSON()
    {
        $request = new RequestMoney($this->mockRequest(201, "XXXXXX"));
        $request->setEmail("email");
        $request->setCurrency("EUR");
        $request->setAmount(10);
        $request->perform($this->getClient());
    }

    /**
     * @expectedException \CashCloud\Api\Exception\CashCloudException
     */
    public function testInvalidNoHash()
    {
        $request = new RequestMoney($this->mockRequest(201, json_encode(array('error'=>false))));
        $request->setEmail("email");
        $request->setCurrency("EUR");
        $request->setAmount(10);
        $request->perform($this->getClient());
    }

    /**
     * @expectedException \CashCloud\Api\Exception\CashCloudException
     * @expectedExceptionMessage Invalid email!
     */
    public function testEmailMustBeSet()
    {
        $request = new RequestMoney();
        $request->setAmount(10);
        $request->setCurrency('EUR');
        $response = $request->perform($this->getClient());
        $this->assertEquals('1123456', $response->hash);
    }

    /**
     * @expectedException \CashCloud\Api\Exception\CashCloudException
     * @expectedExceptionMessage Invalid currency!
     */
    public function testCurrencyMustBeSet()
    {
        $request = new RequestMoney();
        $request->setEmail("email");
        $request->setAmount(10);
        $response = $request->perform($this->getClient());
        $this->assertEquals('1123456', $response->hash);
    }

    /**
     * @expectedException \CashCloud\Api\Exception\CashCloudException
     * @expectedExceptionMessage Invalid currency!
     */
    public function testCurrencyInvalid()
    {
        $request = new RequestMoney();
        $request->setCurrency("USD");
    }

    /**
     * @expectedException \CashCloud\Api\Exception\CashCloudException
     * @expectedExceptionMessage Invalid amount!
     */
    public function testAmountMustBeSet()
    {
        $request = new RequestMoney($this->mockRequest(201, json_encode(array('hash'=>'1123456'))));
        $request->setEmail("email");
        $request->setCurrency('EUR');
        $response = $request->perform($this->getClient());
        $this->assertEquals('1123456', $response->hash);
    }

    public function testGotHash()
    {
        $request = new RequestMoney($this->mockRequest(201, json_encode(array('hash'=>'1123456'))));
        $request->setEmail("email");
        $request->setCurrency("EUR");
        $request->setAmount(10);

        $response = $request->perform($this->getClient());
        $this->assertEquals('1123456', $response->hash);

    }

    public function testAttribtuesAreSet()
    {
        $request = new RequestMoney($this->mockRequest(201, json_encode(array('hash'=>'1123456'))));
        $request->setCurrency("EUR");
        $request->setEmail("email");
        $request->setAmount(10);
        $request->setReason(12);
        $request->setRemark("remark");
        $request->setExternalData("xx", "xx", "desc");
        $this->assertEquals(array (
            'email' => "email",
            'amount' => 10,
            'currency' => 'EUR',
            'reason_id' => 12,
            'remark' => 'remark',
            'extern.id' => 'xx',
            'extern.reference' => 'xx',
            'extern.description' => 'desc',
        ), $request->getData());
    }
}
