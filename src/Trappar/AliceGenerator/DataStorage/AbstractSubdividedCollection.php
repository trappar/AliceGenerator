<?php

namespace Trappar\AliceGenerator\DataStorage;

use Doctrine\Common\Collections\ArrayCollection;
use Trappar\AliceGenerator\Persister\PersisterInterface;

abstract class AbstractSubdividedCollection
{
    /**
     * @var PersisterInterface
     */
    private $persister;

    /**
     * @var array
     */
    private $stores = [];

    public function setPersister(PersisterInterface $persister)
    {
        $this->persister = $persister;
    }

    /**
     * @param $object string|object
     * @return ArrayCollection
     */
    public function getStore($object)
    {
        $subdivision = is_string($object) ? $object : $this->determineSubdivision($object);

        if (!isset($this->stores[$subdivision])) {
            $this->stores[$subdivision] = $this->getBackingStore();
        }

        return $this->stores[$subdivision];
    }

    protected function determineSubdivision($object)
    {
        return $this->persister->getClass($object);
    }

    protected function getBackingStore()
    {
        return new ArrayCollection();
    }
}