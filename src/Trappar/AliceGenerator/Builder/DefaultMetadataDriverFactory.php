<?php

namespace Trappar\AliceGenerator\Builder;

use Doctrine\Common\Annotations\Reader;
use Metadata\Driver\DriverChain;
use Metadata\Driver\FileLocator;
use Trappar\AliceGenerator\Metadata\Driver\AnnotationDriver;
use Trappar\AliceGenerator\Metadata\Driver\YamlDriver;

class DefaultMetadataDriverFactory implements MetadataDriverFactoryInterface
{
    public function createDriver(array $metadataDirs, Reader $annotationReader)
    {
        $annotationDriver = new AnnotationDriver($annotationReader);

        if (!empty($metadataDirs)) {
            $fileLocator = new FileLocator($metadataDirs);

            return new DriverChain([
                new YamlDriver($fileLocator),
                $annotationDriver
            ]);
        } else {
            return $annotationDriver;
        }
    }
}