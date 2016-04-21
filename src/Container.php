<?php

namespace Retry;

class Container
{
    private $procs = [];

    public function set($key, callable $fn)
    {
        $this->procs[$key] = $fn;
    }

    public function execute($times, callable $fn)
    {
        $this->callWithCheck('beforeOnce');
        $ret = null;
        for ($i = 0; $i < $times; $i++) {
            $this->callWithCheck('beforeEach');
            try {
                $ret = $fn($i);
                $this->callWithCheck('afterEach', $ret);
                goto afterOnce;
            } catch (\Exception $e) {
                if ($i === $times - 1) {
                    throw $e;
                }
            }
        }
        afterOnce:
        $this->callWithCheck('afterOnce', $ret);
        $this->procs = [];

        return $ret;
    }

    private function callWithCheck($key, $arg = null)
    {
        if (isset($this->procs[$key])) {
            return $this->procs[$key]($arg);
        }
    }
}
