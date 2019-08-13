<?php namespace CashCloud\Api\Method;

use CashCloud\Api\Exception\CashCloudException;
use CashCloud\Api\Rest\Client;
use CashCloud\Api\Rest\Request;

/**
 * Class RequestMoney
 * @package CashCloud\Method
 */
class RequestMoney extends Method
{
    /**
     * @var string
     */
    protected $email;
    /**
     * @var int
     */
    protected $amount;
    /**
     * @var int
     */
    protected $currency;
    /**
     * @var int
     */
    protected $reason;
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
     * Return request method
     *
     * @return string
     */
    public function getMethod()
    {
        return Request::POST;
    }

    /**
     * Returns cashcloud user email
     *
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets cashcloud user email
     *
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns amount in euro cents
     *
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Sets amount in euro cents
     *
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Returns currency (EUR|CCR)
     *
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Sets currency (EUR|CCR)
     *
     * @param mixed $currency
     * @throws \CashCloud\Api\Exception\CashCloudException
     */
    public function setCurrency($currency)
    {
        if ($currency != Client::CURRENCY_EUR && $currency != Client::CURRENCY_CCR) {
            throw new CashCloudException("Invalid currency!");
        }
        $this->currency = $currency;
    }

    /**
     * Sets external data
     *
     * @param string $id
     * @param string $reference
     * @param string $description
     * @internal param mixed $externalId
     */
    public function setExternalData($id, $reference = null, $description = null)
    {
        $this->externalId = $id;
        $this->externalReference = $reference;
        $this->externalDescription = $description;
    }

    /**
     * Return method URL
     *
     * @param \CashCloud\Api\Rest\Client $api
     * @return string
     */
    public function getUrl(Client $api)
    {
        return $api->getMethodUrl('request');
    }

    /**
     * Return method data
     *
     * @return array
     */
    public function getData()
    {
        return array(
            'email'=>$this->getEmail(),
            'amount'=>$this->getAmount(),
            'currency_id'=>$this->getCurrency(),
            'reason_id'=>$this->getReason(),
            'remark'=>$this->getRemark(),
            'extern.id'=>$this->externalId,
            'extern.reference'=>$this->externalReference,
            'extern.description'=>$this->externalDescription,
        );
    }

    /**
     * Performs the request
     *
     * @param Client $api
     * @return mixed
     * @throws \CashCloud\Api\Exception\CashCloudException
     */
    public function perform(Client $api)
    {
        if($this->getAmount() <= 0) {
            throw new CashCloudException("currency.invalidAmount");
        }

        if(is_null($this->getCurrency())) {
            throw new CashCloudException("currency.invalid");
        }

        if(empty($this->email)) {
            throw new CashCloudException("email.missing");
        }


        return parent::perform($api);
    }


    /**
     * Gets order remark
     *
     * @return mixed
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Sets order remark
     *
     * @param mixed $remark
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    }

    /**
     * Returns order reason
     *
     * @return mixed
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Sets order reason
     *
     * @param mixed $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * Formats the response
     *
     * @param Request $request
     * @throws \CashCloud\Api\Exception\CashCloudException
     * @return array
     */
    public function formatResponse(Request $request)
    {
        $json = $request->getJson();

        if(!$json || empty($json->hash)) {
            throw new CashCloudException("Unable to parse request body: ".$request->getBody());
        }

        return $json;
    }
}
