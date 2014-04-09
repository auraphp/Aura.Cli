<?php
namespace Aura\Cli;

use Aura\Cli\Context\Getopt;

class GetoptFactory
{
    public function __construct(GetoptParser $getopt_parser)
    {
        $this->getopt_parser = $getopt_parser;
    }

    public function newInstance(array $options, array $input)
    {
        $this->getopt_parser->setOptions($options);
        $this->getopt_parser->parse($input);
        return new Getopt(
            $this->getopt_parser->getValues(),
            $this->getopt_parser->getErrors()
        );
    }
}
