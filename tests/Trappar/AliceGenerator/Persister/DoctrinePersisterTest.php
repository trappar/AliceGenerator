<?php

namespace Trappar\AliceGenerator\Tests\Persister;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\Persister\DoctrinePersister;
use Trappar\AliceGenerator\Tests\Fixtures\DoctrinePersisterTester;
use Trappar\AliceGenerator\Tests\Fixtures\User;
use Trappar\AliceGenerator\Tests\Util\FixtureUtils;

class DoctrinePersisterTest extends TestCase
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var DoctrinePersister
     */
    private $persister;

    public function setup()
    {
        $this->em        = FixtureUtils::buildEntityManager(__DIR__ . '/../Entity');
        $this->persister = new DoctrinePersister($this->em);
    }

    public function testGetClass()
    {
        $user = new User();
        $this->assertSame(User::class, $this->persister->getClass($user));

        $proxyUser = $this->em->getProxyFactory()->getProxy(User::class, ['id' => 1]);
        $this->assertSame(User::class, $this->persister->getClass($proxyUser));
    }

    public function testIsObjectManagedByPersister()
    {
        $this->assertInstanceOf(ClassMetadata::class, $this->persister->isObjectManagedByPersister(new User()));
        $this->assertFalse($this->persister->isObjectManagedByPersister(new \stdClass()));
    }

    public function testPreProcess()
    {
        $mock = $this->createMock(get_class($this->em->getProxyFactory()->getProxy(User::class, ['id' => 1])));
        $mock->expects($this->once())
            ->method('__load');

        $this->persister->preProcess($mock);
    }

    public function testIsPropertyNoOp()
    {
        $tester = new DoctrinePersisterTester();

        $mock = $this->createMock(ValueContext::class);
        $mock->method('getContextObject')->willReturn($tester);

        $mock->method('getPropName')->will(
            $this->onConsecutiveCalls('id', 'generatedStrategyNone','mappedProperty', 'unmappedProperty')
        );

        $this->assertTrue($this->persister->isPropertyNoOp($mock));
        $this->assertFalse($this->persister->isPropertyNoOp($mock));
        $this->assertFalse($this->persister->isPropertyNoOp($mock));
        $this->assertTrue($this->persister->isPropertyNoOp($mock));
    }
}