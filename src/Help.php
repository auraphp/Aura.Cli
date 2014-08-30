<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @package Aura.Cli
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Cli;

use Aura\Cli\Context\OptionFactory;

/**
 *
 * Represents the "help" information for a command.
 *
 * @package Aura.Cli
 *
 */
class Help
{
    /**
     *
     * The long-form help text.
     *
     * @var string
     *
     */
    protected $descr;

    /**
     *
     * A option factory.
     *
     * @var OptionFactory
     *
     */
    protected $option_factory = array();

    /**
     *
     * The option definitions.
     *
     * @var array
     *
     */
    protected $options = array();

    /**
     *
     * A single-line summary for the command.
     *
     * @var string
     *
     */
    protected $summary;

    /**
     *
     * One or more single-line usage examples.
     *
     * @var string|array
     *
     */
    protected $usage;

    /**
     *
     * Constructor.
     *
     * @param OptionFactory $option_factory An option factory.
     *
     */
    public function __construct(OptionFactory $option_factory)
    {
        $this->option_factory = $option_factory;
        $this->init();
    }

    /**
     *
     * Use this to initialize the help object in child classes.
     *
     * @return null
     *
     */
    protected function init()
    {
        $this->summary = '';
    }

    /**
     *
     * Sets the option definitions.
     *
     * @param array $options The option definitions.
     *
     * @return null
     *
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     *
     * Gets the option definitions.
     *
     * @return array
     *
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     *
     * Sets the single-line summary.
     *
     * @param string $summary The single-line summary.
     *
     * @return null
     *
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     *
     * Gets the single-line summary.
     *
     * @return string
     *
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     *
     * Sets the usage line(s).
     *
     * @param string|array $usage The usage line(s).
     *
     * @return null
     *
     */
    public function setUsage($usage)
    {
        $this->usage = $usage;
    }

    /**
     *
     * Sets the long-form description.
     *
     * @param string $descr The long-form description.
     *
     * @return null
     *
     */
    public function setDescr($descr)
    {
        $this->descr = $descr;
    }

    /**
     *
     * Gets the formatted help output.
     *
     * @param string $name The command name.
     *
     * @return string
     *
     */
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

    /**
     *
     * Gets the formatted summary output.
     *
     * @param string $name The command name.
     *
     * @return string
     *
     */
    protected function getHelpSummary($name)
    {
        if (! $this->summary) {
            return;
        }

        return "<<bold>>SUMMARY<<reset>>" . PHP_EOL
             . "    <<bold>>{$name}<<reset>> -- " . $this->getSummary()
             . PHP_EOL . PHP_EOL;
    }

    /**
     *
     * Gets the formatted usage output.
     *
     * @param string $name The command name.
     *
     * @return string
     *
     */
    protected function getHelpUsage($name)
    {
        if (! $this->usage) {
            return;
        }

        $text = "<<bold>>USAGE<<reset>>" . PHP_EOL;
        foreach ((array) $this->usage as $usage) {
            if ($usage) {
                $usage = " {$usage}";
            }
            $text .= "    <<ul>>$name<<reset>>{$usage}" . PHP_EOL;
        }
        return $text . PHP_EOL;
    }

    /**
     *
     * Gets the formatted options output.
     *
     * @return string
     *
     */
    protected function getHelpOptions()
    {
        if (! $this->options) {
            return;
        }

        $text = "<<bold>>OPTIONS<<reset>>" . PHP_EOL;
        foreach ($this->options as $string => $descr) {
            $option = $this->option_factory->newInstance($string, $descr);
            $text .= $this->getHelpOption($option). PHP_EOL;
        }
        return $text;
    }

    /**
     *
     * Gets the formatted output for one option.
     *
     * @param StdClass $option An option struct.
     *
     * @return string
     *
     */
    protected function getHelpOption($option)
    {
        $text = "    "
              . $this->getHelpOptionParam($option->name, $option->param, $option->multi)
              . PHP_EOL;

        if ($option->alias) {
            $text .= "    "
                   . $this->getHelpOptionParam($option->alias, $option->param, $option->multi)
                   . PHP_EOL;
        }

        if (! $option->descr) {
            $option->descr = 'No description.';
        }

        return $text
             . "        " . trim($option->descr) . PHP_EOL;
    }

    /**
     *
     * Gets the formatted output for an option param.
     *
     * @param string $name The option name.
     *
     * @param string $param The option param flag.
     *
     * @param bool $multi The option multi flag.
     *
     * @return string
     *
     */
    protected function getHelpOptionParam($name, $param, $multi)
    {
        $text = "{$name}";
        if (strlen($name) == 2) {
            $text .= $this->getHelpOptionParamShort($param);
        } else {
            $text .= $this->getHelpOptionParamLong($param);
        }

        if ($multi) {
            $text .= " [$text [...]]";
        }
        return $text;
    }

    /**
     *
     * Gets the formatted output for a short option param.
     *
     * @param string $param The option param flag.
     *
     * @return string
     *
     */
    protected function getHelpOptionParamShort($param)
    {
        if ($param == 'required') {
            return " <value>";
        }

        if ($param == 'optional') {
            return " [<value>]";
        }
    }

    /**
     *
     * Gets the formatted output for a long option param.
     *
     * @param string $param The option param flag.
     *
     * @return string
     *
     */
    protected function getHelpOptionParamLong($param)
    {
        if ($param == 'required') {
            return "=<value>";
        }

        if ($param == 'optional') {
            return "[=<value>]";
        }
    }

    /**
     *
     * Gets the formatted output for the long-form description.
     *
     * @return string
     *
     */
    public function getHelpDescr()
    {
        if (! $this->descr) {
            return;
        }

        return "<<bold>>DESCRIPTION<<reset>>" . PHP_EOL
             . "    " . trim($this->descr) . PHP_EOL . PHP_EOL;
    }

}
