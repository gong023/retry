<?php

use Retry\Retry;

class TestException extends \Exception {}

class RetryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Retry
     */
    private $retry;

    public function setUp()
    {
        $this->retry = new Retry();
    }

    public function testRetry()
    {
        $ret = $this->retry
            ->beforeEach(function() {})
            ->beforeOnce(function() {})
            ->afterEach(function() {})
            ->afterOnce(function() {})
            ->retry(3, function() { return 1; });

        $this->assertSame(1, $ret);
    }

    /**
     * @expectedException \TestException
     */
    public function testRetryFails()
    {
        $this->retry
            ->beforeEach(function() {})
            ->beforeOnce(function() {})
            ->afterEach(function() {})
            ->afterOnce(function() {})
            ->retry(2, function() { throw new \TestException; });
    }

    public function testIndex()
    {
        $ret = $this->retry->retry(3, function($index) {
            if ($index === 2) {
                return $index;
            }
            throw new TestException;
        });

        $this->assertSame(2, $ret);
    }
}
