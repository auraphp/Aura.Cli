<?php
namespace aura\cli;
class OptionFactory
{
    protected $base = array(
        'name'    => null,
        'long'    => null,
        'short'   => null,
        'param'   => null,
        'multi'   => null,
        'default' => null,
    );
    
    public function newInstance(array $params)
    {
        $params = array_merge($this->base, $params);
        return new Option(
            $params['name'],
            $params['long'],
            $params['short'],
            $params['param'],
            $params['multi'],
            $params['default']
        );
    }
}
