<?php

namespace Trappar\AliceGenerator\Persister;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use Trappar\AliceGenerator\DataStorage\ValueContext;

class DoctrinePersister extends AbstractPersister
{
    /**
     * @var ObjectManager
     */
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function getClass($object)
    {
        return ClassUtils::getClass($object);
    }

    public function isObjectManagedByPersister($object)
    {
        return $this->getMetadata($object);
    }

    public function preProcess($object)
    {
        // Force proxy objects to load data
        if (method_exists($object, '__load')) {
            $object->__load();
        }
    }

    public function isPropertyNoOp(ValueContext $context)
    {
        $classMetadata = $this->getMetadata($context->getContextObject());

        $propName = $context->getPropName();

        // Skip ID properties
        $isId = in_array($propName, $classMetadata->getIdentifier());

        // Skip unmapped properties
        $mapped = true;
        if ($classMetadata instanceof \Doctrine\ORM\Mapping\ClassMetadata) {
            try {
                $classMetadata->getReflectionProperty($propName);
            } catch (\Exception $e) {
                $mapped = false;
            }
        }

        return $isId || !$mapped;
    }

    /**
     * @param $object
     * @return bool|ClassMetadata
     */
    private function getMetadata($object)
    {
        try {
            return $this->om->getMetadataFactory()->getMetadataFor($this->getClass($object));
        } catch (\Exception $e) {
            return false;
        }
    }
}