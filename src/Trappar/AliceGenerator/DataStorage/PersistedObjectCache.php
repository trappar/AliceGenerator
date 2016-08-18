<?php

namespace Trappar\AliceGenerator\DataStorage;

use Doctrine\Common\Collections\ArrayCollection;

class PersistedObjectCache extends AbstractSubdividedCollection
{
    const OBJECT_SKIPPED = 'CACHE_STORE_OBJECT_SKIPPED';
    const OBJECT_NOT_FOUND = 'CACHE_STORE_OBJECT_NOT_FOUND';

    public function add($object)
    {
        $store = $this->getValidStore($object);
        $store->add($object);

        return $store->count();
    }

    public function skip($object)
    {
        $this->getValidStore($object)->removeElement($object);
        $this->getSkippedStore($object)->add($object);
    }

    public function find($object)
    {
        $key = $this->getValidStore($object)->indexOf($object);
        if ($key !== false) {
            return ($key+1);
        }

        if ($this->getSkippedStore($object)->contains($object)) {
            return self::OBJECT_SKIPPED;
        }

        return self::OBJECT_NOT_FOUND;
    }

    /**
     * @param $object
     * @return ArrayCollection
     */
    private function getValidStore($object)
    {
        return $this->getStore($object)->get('valid');
    }

    /**
     * @param $object
     * @return ArrayCollection
     */
    private function getSkippedStore($object)
    {
        return $this->getStore($object)->get('skipped');
    }

    protected function getBackingStore()
    {
        $collection = new ArrayCollection();
        $collection->set('valid', new ArrayCollection());
        $collection->set('skipped', new ArrayCollection());

        return $collection;
    }
}