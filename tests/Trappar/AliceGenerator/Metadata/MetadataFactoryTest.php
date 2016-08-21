<?php

namespace Trappar\AliceGenerator\Tests\Metadata;

use Doctrine\Common\Annotations\AnnotationReader;
use Metadata\MergeableClassMetadata;
use Metadata\MetadataFactory;
use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\Builder\DefaultMetadataDriverFactory;
use Trappar\AliceGenerator\Exception\RuntimeException;
use Trappar\AliceGenerator\Metadata\PropertyMetadata;
use Trappar\AliceGenerator\Tests\Metadata\Fixtures\Bar;
use Trappar\AliceGenerator\Tests\Metadata\Fixtures\Foo;

class MetadataFactoryTest extends TestCase
{
    /**
     * @var DefaultMetadataDriverFactory
     */
    private $driverFactory;

    public function setUp()
    {
        $this->driverFactory = new DefaultMetadataDriverFactory();
    }

    public function testLoadingYaml()
    {
        $metadataFactory = new MetadataFactory($this->driverFactory->createDriver(
            ['Trappar\AliceGenerator\Tests\Metadata\Fixtures' => __DIR__ . '/Fixtures'],
            new AnnotationReader()
        ));

        /** @var MergeableClassMetadata $metadata */
        $metadata                = $metadataFactory->getMetadataForClass(Foo::class);
        $metadata->fileResources = [];
        $metadata->createdAt     = null;

        $this->assertEquals($this->getDesiredClassMetadata(), $metadata);
    }

    public function testLoadingYamlNoMetadataInFile()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageRegExp('/expected metadata/i');

        $metadataFactory = new MetadataFactory($this->driverFactory->createDriver(
            ['Trappar\AliceGenerator\Tests\Metadata\Fixtures' => __DIR__ . '/Fixtures'],
            new AnnotationReader()
        ));

        $metadataFactory->getMetadataForClass(Bar::class);
    }

    public function testLoadingAnnotations()
    {
        $metadataFactory = new MetadataFactory($this->driverFactory->createDriver([], new AnnotationReader()));

        /** @var MergeableClassMetadata $metadata */
        $metadata                = $metadataFactory->getMetadataForClass(Foo::class);
        $metadata->fileResources = [];
        $metadata->createdAt     = null;

        $this->assertEquals($this->getDesiredClassMetadata(), $metadata);
    }

    public function getDesiredClassMetadata()
    {
        $classMeta = new MergeableClassMetadata(Foo::class);

        $dataMeta             = new PropertyMetadata(Foo::class, 'staticData');
        $dataMeta->staticData = 'test';

        $fakerMeta                    = new PropertyMetadata(Foo::class, 'faker');
        $fakerMeta->fakerName         = 'test';
        $fakerMeta->fakerResolverType = 'array';
        $fakerMeta->fakerResolverArgs = ['test'];

        $fakerShortMeta            = new PropertyMetadata(Foo::class, 'fakerShort');
        $fakerShortMeta->fakerName = 'test';

        $ignoredMeta         = new PropertyMetadata(Foo::class, 'ignored');
        $ignoredMeta->ignore = true;

        $classMeta->addPropertyMetadata($dataMeta);
        $classMeta->addPropertyMetadata($fakerMeta);
        $classMeta->addPropertyMetadata($fakerShortMeta);
        $classMeta->addPropertyMetadata($ignoredMeta);

        $classMeta->createdAt = null;

        return $classMeta;
    }
}