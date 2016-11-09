<?php

namespace Trappar\AliceGenerator\Persister;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
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

        // Skip ID properties if they are not part of composite ID
        $ignore = false;
        if ($classMetadata->isIdentifier($propName) && $classMetadata->generatorType != ClassMetadataInfo::GENERATOR_TYPE_NONE && !$classMetadata->isIdentifierComposite){
            $ignore = true;
        }

        // Skip unmapped properties
        $mapped = true;
        if ($classMetadata instanceof \Doctrine\ORM\Mapping\ClassMetadata) {
            try {
                $classMetadata->getReflectionProperty($propName);
            } catch (\Exception $e) {
                $mapped = false;
            }
        }

        return $ignore || !$mapped;
    }

    /**
     * @param $object
     * @return bool|ClassMetadata|ClassMetadataInfo
     */
    protected function getMetadata($object)
    {
        try {
            return $this->om->getClassMetadata($this->getClass($object));
        } catch (\Exception $e) {
            return false;
        }
    }
}