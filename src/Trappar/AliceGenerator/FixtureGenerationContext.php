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
     * @var PersistedObjectConstraints
     */
    private $persistedObjectConstraints;
    /**
     * @var ReferenceNamerInterface
     */
    private $referenceNamer;
    /**
     * @var boolean
     */
    private $excludeDefaultValues = true;

    public static function create()
    {
        return new static();
    }

    public function __construct()
    {
        $this->referenceNamer             = new ClassNamer();
        $this->persistedObjectConstraints = new PersistedObjectConstraints();
        $this->persistedObjectConstraints->setPersister(new NonSpecificPersister());
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

    public function getPersistedObjectConstraints()
    {
        return $this->persistedObjectConstraints;
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
    public function getExcludeDefaultValues()
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
}