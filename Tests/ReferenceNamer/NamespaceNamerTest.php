<?php

namespace Trappar\AliceGenerator\Tests\ReferenceNamer;

use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\ReferenceNamer\NamespaceNamer;
use Trappar\AliceGenerator\Tests\Entity\User;

class NamespaceNamerTest extends TestCase
{
    public function testDefault()
    {
        $this->assertSame(
            'TrapparAliceGeneratorTestsEntityUser-',
            $this->getNamer()->createPrefix(new User())
        );
    }

    public function testIgnoredNamespaces()
    {
        $this->assertSame(
            'TrapparEntityUser-',
            $this->getNamer()
                ->setIgnoredNamespaces(['AliceGenerator', 'Tests'])
                ->createPrefix(new User())
        );
    }

    public function testNamespaceSeparator()
    {
        $this->assertSame(
            'Trappar-AliceGenerator-Tests-Entity-User-',
            $this->getNamer()
                ->setNamespaceSeparator('-')
                ->createPrefix(new User())
        );
    }

    private function getNamer()
    {
        return new NamespaceNamer();
    }
}