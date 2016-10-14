<?php

namespace Trappar\AliceGenerator\Tests\Persister;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\Persister\DoctrinePersister;
use Trappar\AliceGenerator\Tests\Fixtures\DoctrinePersisterTesterComposite;
use Trappar\AliceGenerator\Tests\Util\FixtureUtils;

class DoctrinePersisterCompositeTest extends TestCase
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var DoctrinePersisterTesterComposite
     */
    private $persister;

    public function setup()
    {
        $this->em        = FixtureUtils::buildEntityManager(__DIR__ . '/../Entity');
        $this->persister = new DoctrinePersister($this->em);
    }

    public function testIsPropertyNoOp()
    {
        $tester = new DoctrinePersisterTesterComposite();

        $mock = $this->createMock(ValueContext::class);
        $mock->method('getContextObject')->willReturn($tester);

        $mock->method('getPropName')->will(
            $this->onConsecutiveCalls('id', 'generatedStrategyNone','mappedProperty', 'unmappedProperty')
        );

        $this->assertFalse($this->persister->isPropertyNoOp($mock));
        $this->assertFalse($this->persister->isPropertyNoOp($mock));
        $this->assertFalse($this->persister->isPropertyNoOp($mock));
        $this->assertTrue($this->persister->isPropertyNoOp($mock));
    }
}