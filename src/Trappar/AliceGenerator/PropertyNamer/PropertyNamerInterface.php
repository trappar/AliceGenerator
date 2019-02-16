<?php

namespace Trappar\AliceGenerator\PropertyNamer;

use Trappar\AliceGenerator\DataStorage\ValueContext;

interface PropertyNamerInterface
{
    /**
     * @param ValueContext $context
     * @return string
     */
    public function createName(ValueContext $context);
}
