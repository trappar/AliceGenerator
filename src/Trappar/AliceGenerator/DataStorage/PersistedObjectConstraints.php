<?php

namespace Trappar\AliceGenerator\DataStorage;

use Doctrine\Common\Collections\ArrayCollection;

class PersistedObjectConstraints extends AbstractSubdividedCollection
{
    /**
     * @var bool|int
     */
    private $maximumObjectsPerType = false;

    public function add($object)
    {
        $this->getStore($object)->get('objects')->add($object);
    }
    
    public function checkValid($object)
    {
        $store = $this->getStore($object);

        $checked = $store->get('checked');
        $checkedCount = $checked->count();
        if (!$checked->contains($object)) {
            $checked->add($object);
        }

        $maximum = $store->containsKey('maximum') ? $store->get('maximum') : $this->maximumObjectsPerType;
        if (is_int($maximum) && $checkedCount >= $maximum) {
            return false;
        }

        $objects = $store->get('objects');
        if ($objects->count()) {
            return $objects->contains($object);
        }
        
        return true;
    }

    /**
     * @param int $maximumObjectsPerType
     * @return PersistedObjectConstraints
     */
    public function setMaximumObjectsPerType($maximumObjectsPerType)
    {
        $this->maximumObjectsPerType = $maximumObjectsPerType;

        return $this;
    }

    /**
     * @param object|string $class
     * @param int $maximum
     * @return $this
     */
    public function setMaximumObjectsForType($class, $maximum)
    {
        $this->getStore($class)->set('maximum', $maximum);

        return $this;
    }

    protected function getBackingStore()
    {
        $collection = new ArrayCollection();
        $collection->set('objects', new ArrayCollection());
        $collection->set('checked', new ArrayCollection());

        return $collection;
    }
}