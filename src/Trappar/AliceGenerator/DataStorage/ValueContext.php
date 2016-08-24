<?php

namespace Trappar\AliceGenerator\DataStorage;

use Trappar\AliceGenerator\Metadata\PropertyMetadata;
use Trappar\AliceGenerator\ValueVisitor;

class ValueContext
{
    /**
     * @var ValueVisitor
     */
    private $valueVisitor;

    /**
     * @var PropertyMetadata
     */
    private $metadata;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var object
     */
    private $contextObject;

    /**
     * @var string
     */
    private $contextObjectClass;

    /**
     * @var bool
     */
    private $modified = false;

    /**
     * @var bool
     */
    private $skipped = false;

    public function __construct(
        $value = null,
        $contextObjectClass = null,
        $contextObject = null,
        PropertyMetadata $metadata = null,
        ValueVisitor $valueVisitor = null
    )
    {
        $this->value              = $value;
        $this->metadata           = $metadata;
        $this->contextObject      = $contextObject;
        $this->contextObjectClass = $contextObjectClass;
        $this->valueVisitor       = $valueVisitor;
    }

    /**
     * @return PropertyMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return ValueVisitor
     */
    public function getValueVisitor()
    {
        return $this->valueVisitor;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param      $value
     * @param bool $setModified
     * @return $this
     */
    public function setValue($value, $setModified = true)
    {
        $this->value = $value;
        if ($setModified) {
            $this->modified = true;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContextObject()
    {
        return $this->contextObject;
    }

    public function getContextObjectClass()
    {
        return $this->contextObjectClass;
    }

    /**
     * @return mixed
     */
    public function getPropName()
    {
        return $this->getMetadata()->name;
    }

    /**
     * @return boolean
     */
    public function isModified()
    {
        return $this->modified;
    }

    /**
     * @return boolean
     */
    public function isSkipped()
    {
        return $this->skipped;
    }

    /**
     * @param boolean $skipped
     */
    public function setSkipped($skipped)
    {
        $this->skipped = $skipped;
    }
}