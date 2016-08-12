<?php

namespace Trappar\AliceGenerator;

use Metadata\MetadataFactoryInterface;
use Trappar\AliceGenerator\Metadata\Resolver\MetadataResolverInterface;
use Trappar\AliceGenerator\ObjectHandlerRegistryInterface;
use Trappar\AliceGenerator\Persister\PersisterInterface;

class FixtureGenerator
{
    private $valueVisitor;
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;
    /**
     * @var PersisterInterface
     */
    private $persister;
    /**
     * @var MetadataResolverInterface
     */
    private $propertyValueResolver;
    /**
     * @var ObjectHandlerRegistryInterface
     */
    private $handlerRegistry;
    /**
     * @var YamlWriterInterface
     */
    private $yamlWriter;

    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        PersisterInterface $persister,
        MetadataResolverInterface $propertyValueResolver,
        ObjectHandlerRegistryInterface $handlerRegistry,
        YamlWriterInterface $yamlWriter
    )
    {
        $this->metadataFactory       = $metadataFactory;
        $this->persister             = $persister;
        $this->propertyValueResolver = $propertyValueResolver;
        $this->handlerRegistry       = $handlerRegistry;
        $this->yamlWriter = $yamlWriter;

        $this->valueVisitor = new ValueVisitor($this->metadataFactory, $this->persister, $this->propertyValueResolver, $this->handlerRegistry);
    }

    public function generateArray($value, $fixtureGenerationContext = null)
    {
        if (!$fixtureGenerationContext) {
            $fixtureGenerationContext = new FixtureGenerationContext();
        }

        $this->valueVisitor->setup($fixtureGenerationContext);
        $this->valueVisitor->visitSimpleValue($value);

        return $this->valueVisitor->getResults();
    }

    public function generateYaml($value, $fixtureGenerationContext = null)
    {
        $results = $this->generateArray($value, $fixtureGenerationContext);

        return $this->yamlWriter->write($results);
    }
}