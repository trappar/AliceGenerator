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
     * @var bool
     */
    private $modified = false;

    /**
     * @var bool
     */
    private $skipped = false;

    /**
     * @return PropertyMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param PropertyMetadata $metadata
     * @return ValueContext
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @return ValueVisitor
     */
    public function getValueVisitor()
    {
        return $this->valueVisitor;
    }

    /**
     * @param ValueVisitor $valueVisitor
     * @return ValueContext
     */
    public function setValueVisitor(ValueVisitor $valueVisitor)
    {
        $this->valueVisitor = $valueVisitor;

        return $this;
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
            $this->setModified(true);
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

    /**
     * @param mixed $contextObject
     * @return ValueContext
     */
    public function setContextObject($contextObject)
    {
        $this->contextObject = $contextObject;

        return $this;
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
     * @param boolean $modified
     * @return ValueContext
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
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