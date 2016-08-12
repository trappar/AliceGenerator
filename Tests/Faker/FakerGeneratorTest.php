<?php

namespace Trappar\AliceGenerator\Tests\Faker;

use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\Faker\FakerGenerator;

class FakerGeneratorTest extends TestCase
{
    public function test()
    {
        $result = FakerGenerator::generate('foo', [
            1,
            'hello',
            null,
            true,
            [
                1, 'test'
            ]
        ]);

        $this->assertSame('<foo(1, "hello", null, true, [1, "test"])>', $result);
    }
}