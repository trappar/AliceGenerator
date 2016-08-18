<?php

namespace Trappar\AliceGenerator\Persister;

use Trappar\AliceGenerator\DataStorage\ValueContext;

interface PersisterInterface
{
    /**
     * Should return the class for the given object.
     * example: In DoctrinePersister sometimes objects may be proxies which need to be resolved, so a special utility function
     *          must be used to determine an object's class.
     *
     * @param $object
     * @return string
     */
    public function getClass($object);

    /**
     * Should return true if the object is managed by this persister
     *
     * @param $object
     * @return boolean
     */
    public function isObjectManagedByPersister($object);

    /**
     * Any code which needs to be immediately run on a persisted object to get the object ready for serialization goes
     * here
     *
     * @param $object
     */
    public function preProcess($object);

    /**
     * Should return true if this property should always be skipped during serialization
     * Example: normally true for IDs
     *
     * @param ValueContext $context
     * @return bool
     */
    public function isPropertyNoOp(ValueContext $context);
}