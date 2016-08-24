<?php

namespace Trappar\AliceGenerator\Tests\Metadata\Resolver\Faker;

use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\Metadata\PropertyMetadata;
use Trappar\AliceGenerator\Metadata\Resolver\Faker\ArrayFakerResolver;
use Trappar\AliceGenerator\Tests\Fixtures\User;

class ArrayFakerResolverTest extends TestCase
{
    public function testResolve()
    {
        $resolver = new ArrayFakerResolver();

        $metadata                    = new PropertyMetadata(User::class, 'username');
        $metadata->fakerName         = 'myFaker';
        $metadata->fakerResolverArgs = [1, true, 'test'];

        $valueContext = new ValueContext(null, null, null, $metadata);

        $resolver->resolve($valueContext);

        $this->assertSame('<myFaker(1, true, "test")>', $valueContext->getValue());
    }
}