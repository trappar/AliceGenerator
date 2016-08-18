<?php

namespace Trappar\AliceGenerator\Tests\Metadata\Resolver\Faker;

use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\Exception\FakerResolverException;
use Trappar\AliceGenerator\Metadata\PropertyMetadata;
use Trappar\AliceGenerator\Metadata\Resolver\Faker\ClassFakerResolver;
use Trappar\AliceGenerator\Tests\Entity\User;

class ClassFakerResolverTest extends TestCase
{
    /**
     * @var ClassFakerResolver
     */
    private $resolver;

    public function setup()
    {
        $this->resolver = new ClassFakerResolver();
    }

    /**
     * @dataProvider getTestCases
     * @param string $expected
     * @param array $fakerArgs
     */
    public function testResolve($expected, array $fakerArgs)
    {
        $this->assertSame($expected, $this->runResolve($fakerArgs));
    }

    public function getTestCases()
    {
        return [
            ['test', [self::class]],
            ['test', [self::class.'::toFixture']],
            ['test', [self::class, 'toFixture']],
            ['test', ['self']],
            ['test', ['self::toFixture']],
            ['test', ['self', 'toFixture']],
        ];
    }

    public function testInvalidClass()
    {
        $this->expectException(FakerResolverException::class);
        $this->expectExceptionMessageRegExp('/must be callable/');
        $this->runResolve(['invalid_class']);
    }

    private function runResolve(array $fakerArgs)
    {
        $metadata = new PropertyMetadata(User::class, 'username');
        $metadata->fakerName = 'test';
        $metadata->fakerResolverType = 'class';
        $metadata->fakerResolverArgs = $fakerArgs;

        $context = new ValueContext();
        $context->setMetadata($metadata);
        $context->setContextObject($this);

        $this->resolver->resolve($context);

        return $context->getValue();
    }

    public static function toFixture()
    {
        return 'test';
    }
}