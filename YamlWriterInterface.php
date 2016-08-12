<?php

namespace Trappar\AliceGenerator;

interface YamlWriterInterface
{
    /**
     * @param array $data
     * @return string
     */
    public function write(array $data);
}