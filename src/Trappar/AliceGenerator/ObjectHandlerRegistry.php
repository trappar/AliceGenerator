<?php

namespace Trappar\AliceGenerator;

use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\ObjectHandler\ObjectHandlerInterface;

class ObjectHandlerRegistry implements ObjectHandlerRegistryInterface
{
    /**
     * @var ObjectHandlerInterface[]
     */
    protected $handlers = [];

    public function __construct(array $handlers = [])
    {
        $this->registerHandlers($handlers);
    }

    /**
     * @inheritdoc
     */
    public function registerHandlers(array $handlers)
    {
        foreach ($handlers as $handler) {
            $this->registerHandler($handler);
        }
    }

    /**
     * @inheritdoc
     */
    public function runHandlers(ValueContext $valueContext)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->handle($valueContext)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ObjectHandlerInterface $handler
     */
    private function registerHandler(ObjectHandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }
}