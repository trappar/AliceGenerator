<?php

namespace Trappar\AliceGenerator\Tests\Metadata\Resolver\Faker;

use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\Exception\FakerResolverException;
use Trappar\AliceGenerator\Metadata\PropertyMetadata;
use Trappar\AliceGenerator\Metadata\Resolver\Faker\CallbackFakerResolver;
use Trappar\AliceGenerator\Tests\Fixtures\User;

class CallbackFakerResolverTest extends TestCase
{
    private $testProp = 'testProp';

    /**
     * @var CallbackFakerResolver
     */
    private $resolver;

    public function setup()
    {
        $this->resolver = new CallbackFakerResolver();
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
            ['foo', [self::class, 'toFixtureString']],
            ['foo', ['toFixtureString']],
            ['testProp', ['toFixtureStringNonStatic']],
            ['<myFaker("bar")>', ['toFixtureArray']],
            ['baz', ['toFixtureValueContext']],
        ];
    }

    public function testInvalidClass()
    {
        $this->expectException(FakerResolverException::class);
        $this->expectExceptionMessageRegExp('/must be statically callable/');
        $this->runResolve(['invalid_class', 'toFixture']);
    }

    public function testInvalidMethod()
    {
        $this->expectException(FakerResolverException::class);
        $this->expectExceptionMessageRegExp('/must publicly exist/');
        $this->runResolve(['invalidMethod']);
    }

    public function testTooManyArguments()
    {
        $this->expectException(FakerResolverException::class);
        $this->expectExceptionMessageRegExp('/can only accept one or two/i');
        $this->runResolve([1,2,3,4]);
    }

    private function runResolve(array $fakerArgs)
    {
        $metadata = new PropertyMetadata(User::class, 'username');
        $metadata->fakerName = 'myFaker';
        $metadata->fakerResolverArgs = $fakerArgs;

        $context = new ValueContext();
        $context->setMetadata($metadata);
        $context->setContextObject($this);

        $this->resolver->resolve($context);

        return $context->getValue();
    }

    public static function toFixtureString()
    {
        return 'foo';
    }

    public function toFixtureStringNonStatic()
    {
        return $this->testProp;
    }

    public static function toFixtureArray()
    {
        return ['bar'];
    }

    public static function toFixtureValueContext(ValueContext $context)
    {
        $context->setValue('baz');

        return $context;
    }
}