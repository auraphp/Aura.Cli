<?php
namespace Aura\Cli\Context;

class PropertyFactory
{
    protected $globals;
    
    public function __construct(
        array $globals
    ) {
        $this->globals = $globals;
    }
    
    public function newServer()
    {
        return new Values($this->getGlobals('_SERVER'));
    }
    
    public function newEnv()
    {
        return new Values($this->getGlobals('_ENV'));
    }
    
    public function newOpts(array $data = [])
    {
        return new Values($data);
    }
    
    public function newArgs(array $data = [])
    {
        return new Values($data);
    }
    
    public function newOptsArgs(array $defs)
    {
        $getopt = new Getopt;
        $getopt->setDefs($defs);
        $getopt->setArgv($this->getGlobals('argv'));
        return [
            $this->newOpts($getopt->getOpts()),
            $this->newArgs($getopt->getArgs()),
        ];
    }
    
    protected function getGlobals($key)
    {
        return isset($this->globals[$key])
             ? $this->globals[$key]
             : [];
    }
}
