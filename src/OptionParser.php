<?php
namespace Aura\Cli;

class OptionParser
{
    public function getDefined($string, $descr = null)
    {
        if (is_int($string)) {
            $string = $descr;
            $descr = null;
        }
        
        // option definition array
        $option = array(
            'name'  => null,
            'alias' => null,
            'multi' => false,
            'param' => 'rejected',
            'descr' => $descr,
        );
        
        // is the param optional, required, or rejected?
        if (substr($string, -2) == '::') {
            $string = substr($string, 0, -2);
            $option['param'] = 'optional';
        } elseif (substr($string, -1) == ':') {
            $string = substr($string, 0, -1);
            $option['param'] = 'required';
        }
        
        // remove any remaining colons
        $string = rtrim($string, ':');
        
        // is the option allowed multiple times?
        if (substr($string, -1) == '*') {
            $option['multi'] = true;
            $string = substr($string, 0, -1);
        }
        
        // does the option have an alias?
        $names = explode(',', $string);
        $option['name'] = $this->fixName($names[0]);
        if (isset($names[1])) {
            $option['alias'] = $this->fixName($names[1]);
        }
        
        return $option;
    }

    public function getUndefined($name)
    {
        $name = $this->fixName($name);

        if (strlen($name) == 2) {
            // undefined short flags do not take a param
            return array(
                'name'  => $name,
                'alias' => null,
                'multi' => false,
                'param' => 'rejected',
                'descr' => null,
            );
        }
        
        // undefined long options take an optional param
        return array(
            'name'  => $name,
            'alias' => null,
            'multi' => false,
            'param' => 'optional',
            'descr' => null,
        );
    }
    
    /**
     * 
     * Normalizes the option name.
     * 
     * @param string $name The option character or long name.
     * 
     * @return The fixes name with leading dash or dashes.
     * 
     */
    protected function fixName($name)
    {
        // trim dashes and spaces
        $name = trim($name, ' -');
        if (strlen($name) == 1) {
            return "-$name";
        }
        return "--$name";
    }
}
