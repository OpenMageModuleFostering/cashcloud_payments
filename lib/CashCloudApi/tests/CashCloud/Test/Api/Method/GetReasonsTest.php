<?php
/**
 * File GetReasonsTest.php
 *
 * @version GIT $Id$
 * @package CashCloud\Test\Api\Method
 */
namespace CashCloud\Test\Api\Method;
use CashCloud\Api\Method\GetReasons;
use CashCloud\Test\TestCase;

/**
 * Class GetReasonsTest
 * @package CashCloud\Test\Api\Method
 */
class GetReasonsTest extends TestCase
{
    /**
     * @expectedException \CashCloud\Api\Exception\CashCloudException
     */
    public function testGetReasonsReturnsString()
    {
        $request = new GetReasons($this->mockRequest(200, "xxx"));
        $request->perform($this->getClient());
    }


    public function testGetReasonsReturnsEmpty()
    {
        $request = new GetReasons($this->mockRequest(200, json_encode(array(
            'error'=>false,
            'messages'=> array(),
        ))));

        $response = $request->perform($this->getClient());
        $this->assertEquals(array(), $response);
    }

    public function testGetReasonsReturnsArray()
    {
        $reasons = array(
            "314" => "Electronics",
            "315" => "Computer",
            "316" => "Home Appliances",
            "317" => "Grocery",
            "318" => "Beverages",
        );

        $request = new GetReasons($this->mockRequest(200, json_encode(array(
            'error'=>false,
            'messages'=> $reasons,
        ))));

        $response = $request->perform($this->getClient());
        $this->assertEquals($reasons, $response);
    }
}
