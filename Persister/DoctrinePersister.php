<?php

namespace Trappar\AliceGenerator\Persister;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Trappar\AliceGenerator\DataStorage\ValueContext;

class DoctrinePersister extends AbstractPersister
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
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

        // Skip ID properties
        return in_array($context->getPropName(), $classMetadata->getIdentifier());
    }

    /**
     * @param $object
     * @return bool|ClassMetadata
     */
    private function getMetadata($object)
    {
        try {
            return $this->em->getMetadataFactory()->getMetadataFor($this->getClass($object));
        } catch (\Exception $e) {
            return false;
        }
    }
}