<?php

namespace Trappar\AliceGenerator\Persister;

abstract class AbstractPersister implements PersisterInterface
{
    public function getClass($object)
    {
        return get_class($object);
    }
}
