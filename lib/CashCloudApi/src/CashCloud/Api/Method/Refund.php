<?php namespace CashCloud\Api\Method;

use CashCloud\Api\Rest\Client;
use CashCloud\Api\Rest\Request;

/**
 * Class Settings
 * @package CashCloud\Api\Method
 */
class Refund extends Method
{
    const REFUND_ACCEPTED_STATUS = 3;
	const REFUND_CANCELLED_STATUS = 5;

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
     * @var string
     */
    protected $externalId;
    /**
     * @var string
     */
    protected $externalReference;
    /**
     * @var string
     */
    protected $externalDescription;
    /**
     * Return method URL
     * @param \CashCloud\Api\Rest\Client $api
     * @return string
     */
    public function getUrl(Client $api)
    {
        return $api->getMethodUrl("refund");
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return Request::POST;
    }


    /**
     * Return method data
     * @return array
     */
    public function getData()
    {
        return array(
            'hash'=>$this->hash,
            'amount'=>$this->amount,
            'remark'=>$this->remark,
            'extern[id]'=> $this->externalId,
            'extern[reference]'     => $this->externalReference,
            'extern[description]'   => $this->externalDescription
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    public function formatResponse(Request $request)
    {
        return $request->getJson();
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @param string $remark
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    }

    public function setExternalData($id, $reference = null, $description = null)
    {
        $this->externalId = $id;
        $this->externalReference = $reference;
        $this->externalDescription = $description;
    }
}
