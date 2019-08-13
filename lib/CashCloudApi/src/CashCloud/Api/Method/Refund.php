<?php namespace CashCloud\Api\Method;

use CashCloud\Api\Rest\Client;
use CashCloud\Api\Rest\Request;

/**
 * Class Settings
 * @package CashCloud\Api\Method
 */
class Refund extends Method
{
    /**
     * @var string
     */
    protected $hash;
    /**
     * @var int
     */
    protected $amount;
    /**
     * @var string
     */
    protected $remark;

    /**
     * Return method URL
     *
     * @param \CashCloud\Api\Rest\Client $api
     * @return string
     */
    public function getUrl(Client $api)
    {
        return $api->getMethodUrl("refund");
    }

    /**
     * Return request method
     *
     * @return string
     */
    public function getMethod()
    {
        return Request::POST;
    }


    /**
     * Return method data
     *
     * @return array
     */
    public function getData()
    {
        return array(
            'hash'=>$this->hash,
            'amount'=>$this->amount,
            'remark'=>$this->remark,
        );
    }

    /**
     * Formats response
     *
     * @param Request $request
     * @return array
     */
    public function formatResponse(Request $request)
    {
        return $request->getJson();
    }

    /**
     * Returns cashcloud hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Sets cashcloud hash
     *
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Returns amount in euro cents
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Sets amount in euro cents
     *
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Returns order remark
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Sets order remark
     *
     * @param string $remark
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    }
}
