<?php
namespace aura\cli;
class MockConsole extends Console
{
    public function __get($key)
    {
        return $this->$key;
    }
}
