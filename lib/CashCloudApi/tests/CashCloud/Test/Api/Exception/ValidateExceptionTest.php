<?php namespace CashCloud\Test\Api\Exception;
use CashCloud\Api\Exception\ValidateException;

/**
 * Class ValidationExceptionTest
 * @package CashCloud\Test\Api\Exception
 */
class ValidateExceptionTest extends \CashCloud\Test\TestCase
{
    public function testNoErrors()
    {
        $exception = new ValidateException([]);
        $this->assertFalse($exception->getFirstError());
        $this->assertEquals([], $exception->getErrors());
    }

    public function testSimpleError()
    {
        $exception = new ValidateException(['sampleError']);
        $this->assertEquals('sampleError', $exception->getFirstError());
    }

    public function testMultipleErrors()
    {
        $exception = new ValidateException(['sampleError', 'secondError']);
        $this->assertEquals('sampleError', $exception->getFirstError());
        $this->assertEquals(['sampleError', 'secondError'], $exception->getErrors());
    }

    public function testErrorWithParam()
    {
        $exception = new ValidateException(['sampleError'=>false]);
        $this->assertEquals('sampleError', $exception->getFirstError());
    }

    public function testErrorWithNamedParam()
    {
        $exception = new ValidateException(['sampleError'=>'oneError']);
        $this->assertEquals('sampleError.oneError', $exception->getFirstError());
    }

    public function testErrorWithErrorsArray()
    {
        $exception = new ValidateException([['oneError', 'twoErrors']]);
        $this->assertEquals('oneError', $exception->getFirstError());
    }

    public function testErrorWithParamAndMultipleErrors()
    {
        $exception = new ValidateException(['sampleError'=>['oneError', 'twoErrors']]);
        $this->assertEquals('sampleError.oneError', $exception->getFirstError());
    }

    public function testErrorWithObjectParam()
    {
        $exception = new ValidateException([(object)['sampleError'=>'oneError']]);
        $this->assertEquals('sampleError.oneError', $exception->getFirstError());
    }
}
