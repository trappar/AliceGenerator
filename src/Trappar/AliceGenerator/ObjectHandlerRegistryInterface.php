<?php

namespace Trappar\AliceGenerator;

use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\ObjectHandler\ObjectHandlerInterface;

interface ObjectHandlerRegistryInterface
{
    /**
     * @param ObjectHandlerInterface[] $handlers
     */
    public function registerHandlers(array $handlers);

    /**
     * @param ValueContext $valueContext
     * @return bool
     */
    public function runHandlers(ValueContext $valueContext);
}