<?php namespace CashCloud\Api\Method;

use CashCloud\Api\Rest\Client;
use CashCloud\Api\Rest\Request;

/**
 * Class GetRates
 * @package CashCloud\Api\Method
 */
class GetRates extends Method
{
    /**
     * @var null
     */
    protected $amount = null;

    /**
     * Return method URL
     *
     * @param \CashCloud\Api\Rest\Client $api
     * @return string
     */
    public function getUrl(Client $api)
    {
        return $api->getMethodUrl("currency-rate");
    }

    /**
     * Return method data
     *
     * @return array
     */
    public function getData()
    {
        return array(
            'amount'=>$this->amount,
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
        if(is_null($this->amount)) {
            return $request->getJson("rate");
        } else {
            return $request->getJson("amount");
        }
    }

    /**
     * Returns amount in euro cents
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Sets amount in euro cents
     *
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }
}
