<?php

namespace Trappar\AliceGenerator\ObjectHandler;

use Trappar\AliceGenerator\DataStorage\ValueContext;

interface ObjectHandlerInterface
{
    /**
     * @param ValueContext $valueContext
     * @return bool true if the handler changed the value, false otherwise
     */
    public function handle(ValueContext $valueContext);
}