<?php

namespace Trappar\AliceGenerator\Builder;

use Doctrine\Common\Annotations\Reader;
use Metadata\Driver\DriverInterface;

interface MetadataDriverFactoryInterface
{
    /**
     * @param array $metadataDirs
     * @param Reader $annotationReader
     *
     * @return DriverInterface
     */
    public function createDriver(array $metadataDirs, Reader $annotationReader);
}