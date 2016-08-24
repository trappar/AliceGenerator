<?php

namespace Trappar\AliceGenerator;

use Metadata\MetadataFactoryInterface;
use Trappar\AliceGenerator\Metadata\Resolver\MetadataResolverInterface;
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
    private $metadataResolver;
    /**
     * @var ObjectHandlerRegistryInterface
     */
    private $objectHandlerRegistry;
    /**
     * @var YamlWriterInterface
     */
    private $yamlWriter;

    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        PersisterInterface $persister,
        MetadataResolverInterface $metadataResolver,
        ObjectHandlerRegistryInterface $objectHandlerRegistry,
        YamlWriterInterface $yamlWriter
    )
    {
        $this->metadataFactory       = $metadataFactory;
        $this->persister             = $persister;
        $this->metadataResolver      = $metadataResolver;
        $this->objectHandlerRegistry = $objectHandlerRegistry;
        $this->yamlWriter            = $yamlWriter;

        $this->valueVisitor = new ValueVisitor($this->metadataFactory, $this->persister, $this->metadataResolver, $this->objectHandlerRegistry);
    }

    public function generateArray($value, $fixtureGenerationContext = null)
    {
        if (!$fixtureGenerationContext) {
            $fixtureGenerationContext = new FixtureGenerationContext();
        }

        $this->valueVisitor->setup($fixtureGenerationContext);
        $this->valueVisitor->visitSimpleValue($value);

        $results = $this->valueVisitor->getResults();

        if ($fixtureGenerationContext->isSortResultsEnabled()) {
            ksort($results, SORT_NATURAL);
            foreach ($results as &$objectType) {
                ksort($objectType, SORT_NATURAL);
            }
        }

        return $results;
    }

    public function generateYaml($value, $fixtureGenerationContext = null)
    {
        $results = $this->generateArray($value, $fixtureGenerationContext);

        return $this->yamlWriter->write($results);
    }
}