<?php
namespace Aura\Cli;

use Aura\Cli\Exception;

class MockException extends Exception
{
    protected $message_only = true;
}
