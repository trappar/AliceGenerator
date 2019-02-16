<?php

namespace Trappar\AliceGenerator\PropertyNamer;

use Trappar\AliceGenerator\DataStorage\ValueContext;

class PropertyNamer implements PropertyNamerInterface
{
    /**
     * @inheritdoc
     */
    public function createName(ValueContext $context)
    {
        return $context->getPropName();
    }
}
