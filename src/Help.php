<?php
namespace Aura\Cli;

class Help
{
    protected $descr;
    protected $options = array();
    protected $summary;
    protected $usage;

    public function __construct(OptionParser $option_parser)
    {
        $this->option_parser = $option_parser;
        $this->init();
    }

    protected function init()
    {
        // ...
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    public function setUsage($usage)
    {
        $this->usage = $usage;
    }

    public function setDescr($descr)
    {
        $this->descr = $descr;
    }

    public function getHelp($name)
    {
        $help = $this->getHelpSummary($name)
              . $this->getHelpUsage($name)
              . $this->getHelpDescr()
              . $this->getHelpOptions()
        ;

        if (! $help) {
            $help = "No help available.";
        }

        return rtrim($help) . PHP_EOL;
    }

    protected function getHelpSummary($name)
    {
        if (! $this->summary) {
            return;
        }

        return "<<bold>>SUMMARY<<reset>>" . PHP_EOL
             . "    <<bold>>{$name}<<reset>> -- {$this->summary}"
             . PHP_EOL . PHP_EOL;
    }

    protected function getHelpUsage($name)
    {
        if (! $this->usage) {
            return;
        }

        $text = "<<bold>>USAGE<<reset>>" . PHP_EOL;
        foreach ((array) $this->usage as $usage) {
             $text .= "    <<ul>>$name<<reset>> {$usage}" . PHP_EOL;
        }
        return $text . PHP_EOL;
    }

    protected function getHelpOptions()
    {
        if (! $this->options) {
            return;
        }
        
        $text = "<<bold>>OPTIONS<<reset>>" . PHP_EOL;
        ksort($this->options);
        foreach ($this->options as $string => $descr) {
            $text .= $this->getHelpOption($string, $descr). PHP_EOL;
        }
        return $text;
    }

    protected function getHelpOption($string, $descr)
    {
        // $name, $alias, $multi, $param, $descr
        extract($this->option_parser->getDefined($string, $descr));

        $text = "    "
              . $this->getHelpOptionParam($name, $param, $multi)
              . PHP_EOL;

        if ($alias) {
            $text .= "    "
                   . $this->getHelpOptionParam($alias, $param, $multi)
                   . PHP_EOL;
        }

        if (! $descr) {
            $descr = 'No description.';
        }

        return $text
             . "        " . trim($descr) . PHP_EOL;
    }

    protected function getHelpOptionParam($name, $param, $multi)
    {
        $text = "{$name}";
        if (strlen($name) == 2) {
            $text .= $this->getHelpOptionParamShort($name, $param);
        } else {
            $text .= $this->getHelpOptionParamLong($name, $param);
        }

        if ($multi) {
            $text .= " [$text [...]]";
        }
        return $text;
    }

    protected function getHelpOptionParamShort($name, $param)
    {
        if ($param == 'required') {
            return " <value>";
        }

        if ($param == 'optional') {
            return " [<value>]";
        }
    }

    protected function getHelpOptionParamLong($name, $param)
    {
        if ($param == 'required') {
            return "=<value>";
        }

        if ($param == 'optional') {
            return "[=<value>]";
        }
    }

    public function getHelpDescr()
    {
        if (! $this->descr) {
            return;
        }

        return "<<bold>>DESCRIPTION<<reset>>" . PHP_EOL
             . "    " . trim($this->descr) . PHP_EOL . PHP_EOL;
    }

}
