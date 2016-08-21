<?php

namespace Trappar\AliceGenerator\Tests;

use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\Exception\InvalidArgumentException;
use Trappar\AliceGenerator\FixtureGenerationContext;

class FixtureGenerationContextTest extends TestCase
{
    public function testAddNonObjectAsConstraint()
    {
        $this->expectException(InvalidArgumentException::class);

        FixtureGenerationContext::create()->addPersistedObjectConstraint('test');
    }
}