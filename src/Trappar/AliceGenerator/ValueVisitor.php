<?php

namespace Trappar\AliceGenerator;

use Metadata\MetadataFactoryInterface;
use Trappar\AliceGenerator\DataStorage\PersistedObjectCache;
use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\Exception\UnknownObjectTypeException;
use Trappar\AliceGenerator\Metadata\Resolver\MetadataResolverInterface;
use Trappar\AliceGenerator\Persister\PersisterInterface;

class ValueVisitor
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;
    /**
     * @var PersistedObjectCache
     */
    private $persistedObjectCache;
    /**
     * @var PersisterInterface
     */
    private $persister;
    /**
     * @var MetadataResolverInterface
     */
    private $metadataResolver;
    /**
     * @var ObjectHandlerRegistryInterface
     */
    private $objectHandlerRegistry;

    /**
     * @var FixtureGenerationContext
     */
    private $fixtureGenerationContext;
    /**
     * @var array
     */
    private $results;
    /**
     * @var int
     */
    private $recursionDepth;

    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        PersisterInterface $persister,
        MetadataResolverInterface $metadataResolver,
        ObjectHandlerRegistryInterface $objectHandlerRegistry
    )
    {
        $this->metadataFactory       = $metadataFactory;
        $this->persister             = $persister;
        $this->metadataResolver      = $metadataResolver;
        $this->objectHandlerRegistry = $objectHandlerRegistry;
    }

    public function setup(FixtureGenerationContext $fixtureGenerationContext)
    {
        $this->fixtureGenerationContext = $fixtureGenerationContext;

        // Reset caches
        $this->results              = [];
        $this->persistedObjectCache = new PersistedObjectCache();
        $this->persistedObjectCache->setPersister($this->persister);
        $this->fixtureGenerationContext->getPersistedObjectConstraints()->setPersister($this->persister);
    }

    public function getResults()
    {
        return $this->results;
    }

    public function visitSimpleValue($value)
    {
        $valueContext = new ValueContext();
        $valueContext->setValueVisitor($this);
        $valueContext->setValue($value, false);

        $this->visitUnknownType($valueContext);

        return $valueContext;
    }

    public function visitUnknownType(ValueContext $valueContext)
    {
        if (is_array($valueContext->getValue())) {
            $this->visitArray($valueContext);
        }

        if (is_object($valueContext->getValue())) {
            $this->visitObject($valueContext);
        }
    }

    public function visitObject(ValueContext $valueContext)
    {
        $object = $valueContext->getValue();

        $objectHandled = $this->objectHandlerRegistry->runHandlers($valueContext);

        if (!$objectHandled && $this->persister->isObjectManagedByPersister($object)) {
            if (!$this->fixtureGenerationContext->getPersistedObjectConstraints()->checkValid($object)) {
                $valueContext->setSkipped(true);

                return;
            }

            $result          = $this->persistedObjectCache->find($object);
            $referencePrefix = $this->fixtureGenerationContext->getReferenceNamer()->createPrefix($object);

            switch ($result) {
                case PersistedObjectCache::OBJECT_NOT_FOUND:
                    if ($this->recursionDepth <= $this->fixtureGenerationContext->getMaximumRecursion()) {
                        $key       = $this->persistedObjectCache->add($object);
                        $reference = $referencePrefix . $key;

                        $objectAdded = $this->handlePersistedObject($object, $reference);

                        if ($objectAdded) {
                            $valueContext->setValue('@' . $reference);

                            return;
                        } else {
                            $this->persistedObjectCache->skip($object);
                            $valueContext->setSkipped(true);

                            return;
                        }
                    }
                    break;
                case PersistedObjectCache::OBJECT_SKIPPED:
                    $valueContext->setSkipped(true);

                    return;
                default:
                    $valueContext->setValue('@' . $referencePrefix . $result);

                    return;
            }

            $valueContext->setSkipped(true);
        }

        if (!$valueContext->isSkipped() && !$valueContext->isModified()) {
            throw new UnknownObjectTypeException(sprintf(
                'Object of unknown type "%s" encountered during generation. Unknown types can\'t be serialized ' .
                'directly. You can create an ObjectHandler for this type, or supply metadata on the property for' .
                'how this should be handled.',
                get_class($valueContext->getValue())
            ));
        }
    }

    public function visitArray(ValueContext $valueContext)
    {
        $array = $valueContext->getValue();

        foreach ($array as $key => &$item) {
            $itemValueContext = $this->visitSimpleValue($item);

            if ($itemValueContext->isSkipped()) {
                unset($array[$key]);
            } else {
                $array[$key] = $itemValueContext->getValue();
            }
        }

        if (!count($array)) {
            $valueContext->setSkipped(true);
        } else {
            $valueContext->setValue($array);
        }
    }

    /**
     * @param       $object
     * @param       $reference
     * @return bool if the object was added to the object cache
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \Exception
     */
    private function handlePersistedObject($object, $reference)
    {
        $class = $this->persister->getClass($object);

        $this->persister->preProcess($object);

        // Create a new instance of this class to check values against
        $newObject = new $class();

        $classMetadata = $this->metadataFactory->getMetadataForClass($class);
        $properties    = $classMetadata->propertyMetadata;

        $saveValues = [];
        $this->recursionDepth++;

        foreach ($properties as $metadata) {
            $value        = $metadata->reflection->getValue($object);
            $initialValue = $metadata->reflection->getValue($newObject);

            $valueContext = new ValueContext();
            $valueContext
                ->setValueVisitor($this)
                ->setMetadata($metadata)
                ->setContextObject($object)
                ->setValue($value, false);

            if ($this->persister->isPropertyNoOp($valueContext)) {
                continue;
            }

            $this->metadataResolver->resolve($valueContext);

            if (!$valueContext->isModified()) {
                $value = $valueContext->getValue();

                if ($this->fixtureGenerationContext->getExcludeDefaultValues()) {
                    // Avoid setting unnecessary data
                    if (is_null($value) || is_bool($value) || is_object($value)) {
                        if ($value === $initialValue) {
                            continue;
                        }
                    } else {
                        if ($value == $initialValue) {
                            continue;
                        }
                    }
                }

                $this->visitUnknownType($valueContext);
            }
            if ($valueContext->isSkipped()) {
                continue;
            }

            $saveValues[$valueContext->getPropName()] = $valueContext->getValue();
        }

        $this->recursionDepth--;

        if (!count($saveValues)) {
            return false;
        } else {
            $this->results[$class][$reference] = $saveValues;

            return true;
        }
    }
}