<?php

namespace Trappar\AliceGenerator;

use Trappar\AliceGenerator\DataStorage\PersistedObjectConstraints;
use Trappar\AliceGenerator\Persister\NonSpecificPersister;
use Trappar\AliceGenerator\ReferenceNamer\ClassNamer;
use Trappar\AliceGenerator\ReferenceNamer\ReferenceNamerInterface;

class FixtureGenerationContext
{
    /**
     * @var int
     */
    protected $maximumRecursion = 5;

    protected $persistedObjectConstraints;

    /**
     * @var ReferenceNamerInterface
     */
    protected $referenceNamer;

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
     * @param $object
     * @return FixtureGenerationContext
     */
    public function addPersistedObjectConstraint($object)
    {
        $this->getPersistedObjectConstraints()->add($object);

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
}