<?php namespace CashCloud\Api\Rest;

use CashCloud\Api\Exception\CashCloudException;

/**
 * Class CurlRequest
 * @package CashCloud\Api\Rest
 */
class CurlRequest implements Request
{
    /**
     * @var string
     */
    private $agent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5';

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var string
     */
    protected $url = null;

    /**
     * @var string
     */
    protected $method = self::GET;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var array|null
     */
    protected $response = null;

    /**
     * @var string|null
     */
    private $error;
    /**
     * @var int|null
     */
    private $errorCode;

    /**
     * Construct request object
     *
     * @param string|null $url
     */
    function __construct($url = null)
    {
        $this->url = $url;
    }

    /**
     * Sets request url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Executes request
     *
     * @param string $method
     * @throws \CashCloud\Api\Exception\CashCloudException
     * @return int response code
     */
    public function execute($method = self::GET)
    {
        if($this->getResponseCode() > 0) {
            // already executed
            return $this->getResponseCode();
        }

        if(is_null($this->url)) {
            throw new CashCloudException("Empty API URL!");
        }

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->agent);

        if($this->getMethod() == Request::POST) {
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getData());
        } else {
            curl_setopt($ch, CURLOPT_URL, $this->url . '?' . http_build_query($this->getData()));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getFormattedHeaders());

        $responseBody = curl_exec($ch);
        $responseCode = 0;
        $this->errorCode = curl_errno($ch);
        $this->error = curl_error($ch);

        if($responseBody) {
            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }

        curl_close($ch);

        $this->response = array('code' => $responseCode, 'body' => $responseBody);

        return $this->getResponseCode();
    }

    /**
     * Returns request headers
     *
     * @return array
     */
    public function getFormattedHeaders()
    {
        $headers = array();
        foreach ($this->getHeaders() as $header => $value) {
            $headers[] = "{$header}:{$value}";
        }
        return $headers;
    }

    /**
     * Returns request headers
     *
     * @param array|string $name
     * @return array
     */
    public function getHeaders($name = null)
    {
        if (is_null($name)) {
            return $this->headers;
        } else {
            return $this->headers[$name];
        }
    }

    /**
     * Sets request headers
     *
     * @param string $name
     * @param string $value
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * Returns request data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets request data
     *
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Executes post request
     *
     * @return int
     */
    public function post()
    {
        return $this->execute(self::POST);
    }

    /**
     * Executes get request
     *
     * @return int
     */
    public function get()
    {
        return $this->execute(self::GET);
    }

    /**
     * Returns request method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Sets request method
     *
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Returns parsed json
     *
     * @param null $field
     * @return mixed|object
     */
    public function getJson($field = null)
    {
        $decoded = json_decode($this->getBody());
        return is_null($field) ? $decoded : $decoded->{$field};
    }

    /**
     * Returns request body
     *
     * @return null|string
     */
    public function getBody()
    {
        return is_null($this->response) ? null : $this->response['body'];
    }

    /**
     * Returns response code
     *
     * @return int|null
     */
    public function getResponseCode()
    {
        return is_null($this->response) ? null : $this->response['code'];
    }

    /**
     * Returns cashcloud error
     *
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Returns error code
     *
     * @return int|null
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
