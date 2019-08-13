<?php namespace CashCloud\Api\Exception;

/**
 * Class ValidateException
 * @package CashCloud\Api\Exception
 */
class ValidateException extends CashCloudException
{
    /**
     * @var array
     */
    protected $errors = array();

    /**
     * Parses errors on construction
     *
     * @param mixed $errors
     */
    public function __construct($errors)
    {
        $this->errors = $this->parseErrors($errors);
        parent::__construct($this->getFirstError(), 412, null);
    }

    /**
     * Returns array of errors
     *
     * @return array|string
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Return first error
     *
     * @return bool
     */
    public function getFirstError()
    {
        foreach ($this->errors as $error) {
            return $error;
        }
        return false;
    }

    /**
     * Parse cashcloud API errors
     *
     * @param $errors
     * @return mixed
     */
    public function parseErrors($errors)
    {
        $formattedErrors = array();

        foreach ($errors as $key=>$value) {
            if(is_object($value)) {
                $value = (array)$value;
            }

            if(is_int($key) && is_string($value)) {
                $formattedErrors[] = $value;
            } elseif(is_string($key) && is_bool($value)) {
                $formattedErrors[] = $key;
            } elseif(is_string($key) && is_string($value)) {
                $formattedErrors[] = $key.'.'.$value;
            } elseif(is_int($key) && is_array($value)) {
                foreach ($value as $subKey => $subError) {
                    $formattedErrors[] = is_int($subKey) ? $subError : "{$subKey}.{$subError}";
                }
            } elseif( is_string($key) && is_array($value)) {
                foreach ($value as $subError) {
                    $formattedErrors[] = $key.'.'.$subError;
                }
            }
        }

        return $formattedErrors;
    }
}
