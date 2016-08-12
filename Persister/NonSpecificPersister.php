<?php

namespace Trappar\AliceGenerator\Persister;

use Trappar\AliceGenerator\DataStorage\ValueContext;

class NonSpecificPersister extends AbstractPersister
{
    /**
     * @inheritDoc
     */
    public function isObjectManagedByPersister($object)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function preProcess($object)
    {
    }

    /**
     * @inheritDoc
     */
    public function isPropertyNoOp(ValueContext $context)
    {
        return false;
    }

}