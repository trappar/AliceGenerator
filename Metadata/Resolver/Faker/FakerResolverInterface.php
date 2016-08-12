<?php

namespace Trappar\AliceGenerator\Metadata\Resolver\Faker;

use Trappar\AliceGenerator\Metadata\Resolver\MetadataResolverInterface;

interface FakerResolverInterface extends MetadataResolverInterface
{
    /**
     * @return string
     */
    public function getType();
}