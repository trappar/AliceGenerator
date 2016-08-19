<?php

namespace Trappar\AliceGenerator\Tests;

use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\Exception\InvalidArgumentException;
use Trappar\AliceGenerator\FixtureGeneratorBuilder;
use Trappar\AliceGenerator\Metadata\Resolver\MetadataResolver;
use Trappar\AliceGenerator\ObjectHandlerRegistryInterface;
use Trappar\AliceGenerator\Tests\Util\FixtureUtils;

class FixtureGeneratorBuilderTest extends TestCase
{
    public function testBuildFixtureGenerator()
    {
        $this->assertInstanceOf('Trappar\AliceGenerator\FixtureGenerator', FixtureUtils::buildFixtureGenerator());
    }

    public function testConfiguringObjectHandlerRegistry()
    {
        FixtureGeneratorBuilder::create()
            ->configureObjectHandlerRegistry(function ($registry) {
                $this->assertInstanceOf(ObjectHandlerRegistryInterface::class, $registry);
            });
    }

    public function testConfiguringMetadataResolver()
    {
        FixtureGeneratorBuilder::create()
            ->configureMetadataResolver(function ($registry) {
                $this->assertInstanceOf(MetadataResolver::class, $registry);
            });
    }

    public function testAddInvalidMetadataDir()
    {
        $this->expectException(InvalidArgumentException::class);
        FixtureGeneratorBuilder::create()->addMetadataDir('asdf');
    }
}