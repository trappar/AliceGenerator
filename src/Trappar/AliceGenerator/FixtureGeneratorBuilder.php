<?php

namespace Trappar\AliceGenerator;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Metadata\MetadataFactory;
use Trappar\AliceGenerator\Builder\DefaultMetadataDriverFactory;
use Trappar\AliceGenerator\Builder\MetadataDriverFactoryInterface;
use Trappar\AliceGenerator\Exception\InvalidArgumentException;
use Trappar\AliceGenerator\Metadata\Resolver\Faker\ArrayFakerResolver;
use Trappar\AliceGenerator\Metadata\Resolver\Faker\CallbackFakerResolver;
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
    private $objectHandlerRegistry;
    /**
     * @var bool
     */
    private $objectHandlersConfigured;
    /**
     * @var YamlWriterInterface
     */
    private $yamlWriter;
    /**
     * @var bool
     */
    private $strictTypeChecking = true;

    public static function create()
    {
        return new static();
    }

    public function __construct()
    {
        $this
            ->setMetadataDriverFactory(new DefaultMetadataDriverFactory())
            ->setPersister(new NonSpecificPersister())
            ->setAnnotationReader(new AnnotationReader())
            ->setMetadataResolver(
                new MetadataResolver([
                    new ArrayFakerResolver(),
                    new CallbackFakerResolver(),
                    new ValueAsArgFakerResolver(),
                    new NoArgFakerResolver()
                ])
            )
            ->setObjectHandlerRegistry(new ObjectHandlerRegistry())
            ->setYamlWriter(new YamlWriter(3, 4));
    }

    /**
     * Adds a directory where the FixtureGenerator will look for class metadata.
     *
     * See: doc/configuration.md
     *
     * @param        $dir
     * @param string $namespacePrefix
     * @return $this
     */
    public function addMetadataDir($dir, $namespacePrefix = '')
    {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
        }

        $this->metadataDirs[$namespacePrefix] = $dir;

        return $this;
    }

    /**
     * Adds a map of namespace prefixes to directories.
     *
     * @param array<string, string> $namespacePrefixToDirMap
     * @return $this
     */
    public function addMetadataDirs(array $namespacePrefixToDirMap)
    {
        foreach ($namespacePrefixToDirMap as $prefix => $dir) {
            $this->addMetadataDir($dir, $prefix);
        }
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

    public function configureMetadataResolver(\Closure $closure)
    {
        $closure($this->metadataResolver);

        return $this;
    }

    /**
     * @param ObjectHandlerRegistryInterface $objectHandlerRegistry
     * @return FixtureGeneratorBuilder
     */
    public function setObjectHandlerRegistry(ObjectHandlerRegistryInterface $objectHandlerRegistry)
    {
        $this->objectHandlerRegistry    = $objectHandlerRegistry;

        return $this;
    }

    public function addDefaultObjectHandlers()
    {
        $this->objectHandlersConfigured = true;
        $this->objectHandlerRegistry->registerHandlers([
            new CollectionHandler(),
            new DateTimeHandler(),
        ]);

        return $this;
    }

    public function configureObjectHandlerRegistry(\Closure $closure)
    {
        $this->objectHandlersConfigured = true;
        $closure($this->objectHandlerRegistry);

        return $this;
    }

    public function setYamlWriter(YamlWriterInterface $yamlWriter)
    {
        $this->yamlWriter = $yamlWriter;

        return $this;
    }

    public function setStrictTypeChecking($enabled)
    {
        $this->strictTypeChecking = $enabled;

        return $this;
    }

    /**
     * @return FixtureGenerator
     */
    public function build()
    {
        $metadataFactory = new MetadataFactory(
            $this->metadataDriverFactory->createDriver($this->metadataDirs, $this->annotationReader)
        );

        if (!$this->objectHandlersConfigured) {
            $this->addDefaultObjectHandlers();
        }

        return new FixtureGenerator(
            new ValueVisitor(
                $metadataFactory,
                $this->persister,
                $this->metadataResolver,
                $this->objectHandlerRegistry,
                $this->strictTypeChecking
            ),
            $this->yamlWriter
        );
    }
}