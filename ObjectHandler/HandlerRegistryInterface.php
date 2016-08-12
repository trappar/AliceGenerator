<?php

namespace Trappar\AliceGenerator\ObjectHandler;

use Trappar\AliceGenerator\DataStorage\ValueContext;

interface HandlerRegistryInterface
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