<?php

namespace Trappar\AliceGenerator\Tests\Metadata\Resolver\Faker;

use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\Metadata\PropertyMetadata;
use Trappar\AliceGenerator\Metadata\Resolver\Faker\ValueAsArgFakerResolver;
use Trappar\AliceGenerator\Tests\Fixtures\User;

class ValueAsArgFakerResolverTest extends TestCase
{
    public function testResolve()
    {
        $resolver = new ValueAsArgFakerResolver();

        $metadata                    = new PropertyMetadata(User::class, 'username');
        $metadata->fakerName         = 'myFaker';
        $metadata->fakerResolverType = 'value-as-arg';

        $context = new ValueContext();
        $context->setMetadata($metadata);
        $context->setValue('foo');

        $resolver->resolve($context);

        $this->assertSame('<myFaker("foo")>', $context->getValue());
    }
}
