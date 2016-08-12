<?php

namespace Trappar\AliceGenerator\Tests\DataStorage;

use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\DataStorage\PersistedObjectCache;
use Trappar\AliceGenerator\Persister\NonSpecificPersister;

class PersistedObjectCacheTest extends TestCase
{
    public function test()
    {
        $cache = new PersistedObjectCache();
        $cache->setPersister(new NonSpecificPersister());

        $testData = new \stdClass();

        $this->assertSame(
            PersistedObjectCache::OBJECT_NOT_FOUND,
            $cache->find($testData)
        );

        $cache->add($testData);

        $this->assertSame(
            1,
            $cache->find($testData)
        );

        $cache->skip($testData);

        $this->assertSame(
            PersistedObjectCache::OBJECT_SKIPPED,
            $cache->find($testData)
        );
    }
}