<?php

namespace Trappar\AliceGenerator\Tests\Util;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Nelmio\Alice\Fixtures\Loader;
use Trappar\AliceGenerator\FixtureGenerationContext;
use Trappar\AliceGenerator\FixtureGenerator;
use Trappar\AliceGenerator\FixtureGeneratorBuilder;
use Trappar\AliceGenerator\Persister\DoctrinePersister;

class FixtureUtils
{
    /**
     * @param $setMetadataDirs
     * @return FixtureGeneratorBuilder
     */
    public static function buildFixtureGeneratorBuilder($setMetadataDirs)
    {
        $entitiesPaths = ['Trappar\AliceGenerator\Tests\Entities' => __DIR__ . '/../Entities'];
        $em = self::buildEntityManager($entitiesPaths);

        $fgBuilder = new FixtureGeneratorBuilder();
        if ($setMetadataDirs) {
            $fgBuilder->setMetadataDirs($entitiesPaths);
        }
        $fgBuilder->setPersister(new DoctrinePersister($em));

        return $fgBuilder;
    }

    /**
     * @param bool $setMetadataDirs
     * @return FixtureGenerator
     */
    public static function buildFixtureGenerator($setMetadataDirs = true)
    {
        return self::buildFixtureGeneratorBuilder($setMetadataDirs)->build();
    }

    /**
     * @param $entitiesDirs
     * @return EntityManager
     */
    public static function buildEntityManager($entitiesDirs)
    {
        $config = Setup::createConfiguration(true);

        $driver = new AnnotationDriver(new AnnotationReader(), $entitiesDirs);

        $config->setMetadataDriverImpl($driver);

        $conn = array(
            'driver' => 'pdo_sqlite',
            'path'   => __DIR__ . '/db.sqlite',
        );

        return EntityManager::create($conn, $config);
    }

    public static function getObjectsFromFixtures(array $data)
    {
        $loader = new Loader();
        return $loader->load($data);
    }

    public static function getFixturesFromObjects($objects, FixtureGenerationContext $context = null)
    {
        return self::buildFixtureGenerator(false)->generateArray($objects, $context);
    }

    public static function convertObjectToFixtureAndBack($objects, FixtureGenerationContext $context = null)
    {
        $fixtures = self::getFixturesFromObjects($objects, $context);
        return self::getObjectsFromFixtures($fixtures);
    }
}