<?php namespace CashCloud\Api\Rest;

/**
 * Interface Request
 * @package CashCloud\Api\Rest
 */
interface Request
{
    const POST = "post";
    const GET = "get";

    /**
     * @return string
     */
    public function getBody();

    /**
     * Sets header
     *
     * @param string $name
     * @param string $value
     */
    public function setHeader($name, $value);

    /**
     * Returns header
     *
     * @param array|string $name
     * @return array
     */
    public function getHeaders($name = null);

    /**
     * Returns method
     *
     * @return string
     */
    public function getMethod();

    /**
     * Returns json
     *
     * @param string $field
     * @return object
     */
    public function getJson($field = null);

    /**
     * Sets data
     *
     * @param array $data
     */
    public function setData($data);

    /**
     * Sets URL
     *
     * @param string $url
     */
    public function setUrl($url);

    /**
     * Sets method
     *
     * @param string $method
     */
    public function setMethod($method);

    /**
     * Returns data
     *
     * @return array
     */
    public function getData();

    /**
     * Returns response
     *
     * @return int
     */
    public function getResponseCode();

    /**
     * Returns error
     *
     * @return string|null
     */
    public function getError();

    /**
     * Returns error code
     *
     * @return int|null
     */
    public function getErrorCode();

    /**
     * Executes request
     *
     * @param string $method
     * @return int response code
     */
    public function execute($method = self::GET);
}
