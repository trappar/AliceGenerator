<?php

namespace Trappar\AliceGenerator;

use Symfony\Component\Yaml\Yaml;

class YamlWriter implements YamlWriterInterface
{
    /**
     * @var int
     */
    private $inline;
    /**
     * @var int
     */
    private $indent;

    public function __construct($inline, $indent)
    {
        $this->inline = $inline;
        $this->indent = $indent;
    }

    /**
     * @inheritDoc
     */
    public function write(array $data)
    {
        return Yaml::dump($data, $this->inline, $this->indent);
    }
}