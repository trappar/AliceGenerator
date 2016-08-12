<?php

namespace Trappar\AliceGenerator\Tests\Persister;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\Persister\DoctrinePersister;
use Trappar\AliceGenerator\Tests\Entity\Post;
use Trappar\AliceGenerator\Tests\Entity\User;
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
        $user = new User();

        $mock = $this->createMock(ValueContext::class);
        $mock->method('getContextObject')->willReturn($user);
        $mock->method('getPropName')->will($this->onConsecutiveCalls('id', 'username'));

        $this->assertTrue($this->persister->isPropertyNoOp($mock));
        $this->assertFalse($this->persister->isPropertyNoOp($mock));
    }

    public function test()
    {

//        $em = FixtureUtils::buildEntityManager(__DIR__ . '/../Entity');
//

//        $mock->username = 'test';
//
//        $doctrinePersister = $this->createMock(DoctrinePersister::class);
////        $doctrinePersister->method('getMetadata')->willReturn($em->getClassMetadata(User::class));
//        $doctrinePersister->method('isObjectManagedByPersister')->willReturn(true);
//
//        $fgBuilder = FixtureUtils::buildFixtureGeneratorBuilder([]);
//        $fgBuilder->setPersister($doctrinePersister);
//
//        $fg = $fgBuilder->build();
//
//        $results = $fg->generateArray($mock);
//
//        xdebug_break();
    }
}