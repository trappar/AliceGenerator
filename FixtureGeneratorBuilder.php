<?php

namespace Trappar\AliceGenerator;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Metadata\MetadataFactory;
use Trappar\AliceGenerator\Builder\DefaultMetadataDriverFactory;
use Trappar\AliceGenerator\Builder\MetadataDriverFactoryInterface;
use Trappar\AliceGenerator\Metadata\Resolver\Faker\ArrayFakerResolver;
use Trappar\AliceGenerator\Metadata\Resolver\Faker\ClassFakerResolver;
use Trappar\AliceGenerator\Metadata\Resolver\Faker\NoArgFakerResolver;
use Trappar\AliceGenerator\Metadata\Resolver\Faker\ValueAsArgFakerResolver;
use Trappar\AliceGenerator\Metadata\Resolver\MetadataResolver;
use Trappar\AliceGenerator\Metadata\Resolver\MetadataResolverInterface;
use Trappar\AliceGenerator\ObjectHandler\CollectionHandler;
use Trappar\AliceGenerator\ObjectHandler\DateTimeHandler;
use Trappar\AliceGenerator\Persister\NonSpecificPersister;
use Trappar\AliceGenerator\Persister\PersisterInterface;

class FixtureGeneratorBuilder
{
    /**
     * @var array
     */
    private $metadataDirs = [];
    /**
     * @var MetadataDriverFactoryInterface
     */
    private $metadataDriverFactory;
    /**
     * @var PersisterInterface
     */
    private $persister;
    /**
     * @var Reader
     */
    private $annotationReader;
    /**
     * @var MetadataResolverInterface
     */
    private $metadataResolver;
    /**
     * @var ObjectHandlerRegistryInterface
     */
    private $handlerRegistry;
    /**
     * @var bool
     */
    private $handlersConfigured = false;
    /**
     * @var YamlWriterInterface
     */
    private $yamlWriter;

    public function __construct()
    {
        $this
            ->setMetadataDriverFactory(new DefaultMetadataDriverFactory())
            ->setPersister(new NonSpecificPersister())
            ->setAnnotationReader(new AnnotationReader())
            ->setMetadataResolver(
                new MetadataResolver([
                    new ArrayFakerResolver(),
                    new ClassFakerResolver(),
                    new ValueAsArgFakerResolver(),
                    new NoArgFakerResolver()
                ])
            )
            ->setHandlerRegistry(new ObjectHandlerRegistry())
            ->setYamlWriter(new YamlWriter(3, 4));
    }

    /**
     * @param array $metadataDirs
     * @return FixtureGeneratorBuilder
     */
    public function setMetadataDirs(array $metadataDirs)
    {
        $this->metadataDirs = $metadataDirs;

        return $this;
    }

    /**
     * @param DefaultMetadataDriverFactory $metadataDriverFactory
     * @return FixtureGeneratorBuilder
     */
    public function setMetadataDriverFactory(DefaultMetadataDriverFactory $metadataDriverFactory)
    {
        $this->metadataDriverFactory = $metadataDriverFactory;

        return $this;
    }

    /**
     * @param mixed $persister
     * @return FixtureGeneratorBuilder
     */
    public function setPersister(PersisterInterface $persister)
    {
        $this->persister = $persister;

        return $this;
    }

    /**
     * @param Reader $annotationReader
     * @return FixtureGeneratorBuilder
     */
    public function setAnnotationReader(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;

        return $this;
    }

    /**
     * @param MetadataResolver $metadataResolver
     * @return FixtureGeneratorBuilder
     */
    public function setMetadataResolver(MetadataResolver $metadataResolver)
    {
        $this->metadataResolver = $metadataResolver;

        return $this;
    }

    /**
     * @param ObjectHandlerRegistryInterface $handlerRegistry
     * @return FixtureGeneratorBuilder
     */
    public function setHandlerRegistry(ObjectHandlerRegistryInterface $handlerRegistry)
    {
        $this->handlerRegistry = $handlerRegistry;

        return $this;
    }

    public function addDefaultHandlers()
    {
        $this->handlersConfigured = true;

        $this->handlerRegistry->registerHandlers([
            new CollectionHandler(),
            new DateTimeHandler(),
        ]);
    }

    public function setYamlWriter(YamlWriterInterface $yamlWriter)
    {
        $this->yamlWriter = $yamlWriter;
    }

    /**
     * @return FixtureGenerator
     */
    public function build()
    {
        $metadataFactory = new MetadataFactory(
            $this->metadataDriverFactory->createDriver($this->metadataDirs, $this->annotationReader)
        );

        if (!$this->handlersConfigured) {
            $this->addDefaultHandlers();
        }

        return new FixtureGenerator(
            $metadataFactory,
            $this->persister,
            $this->metadataResolver,
            $this->handlerRegistry,
            $this->yamlWriter
        );
    }
}