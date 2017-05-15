<?php

namespace Trappar\AliceGenerator;

use Trappar\AliceGenerator\DataStorage\PersistedObjectConstraints;
use Trappar\AliceGenerator\Exception\InvalidArgumentException;
use Trappar\AliceGenerator\Persister\NonSpecificPersister;
use Trappar\AliceGenerator\ReferenceNamer\ClassNamer;
use Trappar\AliceGenerator\ReferenceNamer\ReferenceNamerInterface;

class FixtureGenerationContext
{
    /**
     * @var int
     */
    private $maximumRecursion = 5;

    /**
     * @var int
     */
    private $maximumCollectionChilds; // TODO: set to null to leave current behaviour. Maybe, lets set some default value, like 10?

    /**
     * @var array
     */
    private $entityCollectionLimits = [];

    /**
     * @var PersistedObjectConstraints
     */
    private $persistedObjectConstraints;
    /**
     * @var ReferenceNamerInterface
     */
    private $referenceNamer;
    /**
     * @var bool
     */
    private $excludeDefaultValues = true;
    /**
     * @var bool
     */
    private $sortResults = true;

    public function __construct()
    {
        $this->referenceNamer             = new ClassNamer();
        $this->persistedObjectConstraints = new PersistedObjectConstraints();
        $this->persistedObjectConstraints->setPersister(new NonSpecificPersister());
    }

    public static function create()
    {
        return new static();
    }

    /**
     * @return int
     */
    public function getMaximumRecursion()
    {
        return $this->maximumRecursion;
    }

    /**
     * @param int $max
     * @return FixtureGenerationContext
     */
    public function setMaximumRecursion($max)
    {
        $this->maximumRecursion = $max;

        return $this;
    }

    /**
     * @param array|object $objects
     * @return FixtureGenerationContext
     */
    public function addPersistedObjectConstraint($objects)
    {
        $objects = is_array($objects) ? $objects : [$objects];

        foreach ($objects as $object) {
            if (!is_object($object)) {
                throw new InvalidArgumentException(sprintf(
                    'Non-object passed to addPersistedObjectConstraint() - "%s" given', gettype($object)
                ));
            }
            $this->getPersistedObjectConstraints()->add($object);
        }

        return $this;
    }

    public function getPersistedObjectConstraints()
    {
        return $this->persistedObjectConstraints;
    }

    /**
     * @return ReferenceNamerInterface
     */
    public function getReferenceNamer()
    {
        return $this->referenceNamer;
    }

    /**
     * @param ReferenceNamerInterface $referenceNamer
     * @return FixtureGenerationContext
     */
    public function setReferenceNamer(ReferenceNamerInterface $referenceNamer)
    {
        $this->referenceNamer = $referenceNamer;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isExcludeDefaultValuesEnabled()
    {
        return $this->excludeDefaultValues;
    }

    /**
     * @param boolean $excludeDefaultValues
     * @return FixtureGenerationContext
     */
    public function setExcludeDefaultValues($excludeDefaultValues)
    {
        $this->excludeDefaultValues = $excludeDefaultValues;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSortResultsEnabled()
    {
        return $this->sortResults;
    }

    /**
     * @param boolean $sortResults
     * @return FixtureGenerationContext
     */
    public function setSortResults($sortResults)
    {
        $this->sortResults = $sortResults;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaximumCollectionChilds()
    {
        return $this->maximumCollectionChilds;
    }

    /**
     * @param int $maximumCollectionChilds
     * @return self
     */
    public function setMaximumCollectionChilds($maximumCollectionChilds)
    {
        $this->maximumCollectionChilds = $maximumCollectionChilds;
        return $this;
    }

    public function getCollectionLimit($entityClassName)
    {
        if(isset($this->entityCollectionLimits[$entityClassName])) {
            return $this->entityCollectionLimits[$entityClassName];
        } elseif($this->maximumCollectionChilds) {
            return $this->maximumCollectionChilds;
        } else {
            return false;
        }
    }

    public function setEntityCollectionLimit($entityClassName,$limit)
    {
        $this->entityCollectionLimits[$entityClassName] = $limit;
        return $this;
    }
}