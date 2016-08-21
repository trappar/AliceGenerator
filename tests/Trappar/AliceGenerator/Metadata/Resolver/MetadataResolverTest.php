<?php

namespace Trappar\AliceGenerator\Tests\Metadata\Resolver;

use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\Exception\FakerResolverException;
use Trappar\AliceGenerator\Metadata\PropertyMetadata;
use Trappar\AliceGenerator\Metadata\Resolver\Faker\CallbackFakerResolver;
use Trappar\AliceGenerator\Metadata\Resolver\MetadataResolver;
use Trappar\AliceGenerator\Tests\Fixtures\User;

class MetadataResolverTest extends TestCase
{
    public function testInvalidTypeNoTypes()
    {
        $this->expectException(FakerResolverException::class);
        $this->expectExceptionMessageRegExp('/no faker resolver.*no faker resolver types/i');

        $this->getResolver()->resolve($this->getValueContext());
    }

    public function testInvalidTypeWithAvailableTypes()
    {
        $this->expectException(FakerResolverException::class);
        $this->expectExceptionMessageRegExp('/no faker resolver.*available types are/i');

        $resolver = $this->getResolver();
        $resolver->addFakerResolver(new CallbackFakerResolver());

        $resolver->resolve($this->getValueContext());
    }

    private function getResolver()
    {
        return new MetadataResolver();
    }

    private function getValueContext()
    {
        $metadata = new PropertyMetadata(User::class, 'username');
        $metadata->fakerName = 'test';
        $metadata->fakerResolverType = 'invalid';

        $valueContext = new ValueContext();
        $valueContext->setMetadata($metadata);

        return $valueContext;
    }
}