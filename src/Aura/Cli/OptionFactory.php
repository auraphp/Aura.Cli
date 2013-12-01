<?php
/**
 *
 * This file is part of the Aura project for PHP.
 *
 * @package Aura.Cli
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Cli;

use Aura\Cli\Option;

/**
 *
 * A factory to create Option objects.
 *
 * @package Aura.Cli
 *
 */
class OptionFactory
{
    /**
     *
     * An array of default parameters for Option objects.
     *
     * @var array
     *
     */
    protected $params = [
        'name'    => null,
        'long'    => null,
        'short'   => null,
        'param'   => null,
        'multi'   => null,
        'default' => null,
    ];

    /**
     *
     * Creates and returns a new Option object.
     *
     * @param array $args An array of key-value pairs corresponding to
     * Option constructor params.
     *
     * @return Option
     *
     */
    public function newInstance(array $args)
    {
        $args = array_merge($this->params, $args);
        
        // always need a name
        if (! $args['name']) {
            throw new Exception\OptionName;
        }

        // always need a long format or a short format.
        if (! $args['long'] && ! $args['short']) {
            // auto-add a long format
            $args['long'] = str_replace('_', '-', $args['name']);
        }

        // always need a param value
        if (! $args['param']) {
            $args['param'] = Option::PARAM_OPTIONAL;            
        }
        
        $ok = $args['param'] === Option::PARAM_REQUIRED
           || $args['param'] === Option::PARAM_REJECTED
           || $args['param'] === Option::PARAM_OPTIONAL;

        if (! $ok) {
            throw new Exception\OptionParam;
        }

        return new Option(
            $args['name'],
            $args['long'],
            $args['short'],
            $args['param'],
            $args['multi'],
            $args['default']
        );
    }
}
