<?php

namespace Trappar\AliceGenerator\Metadata;

use Metadata\PropertyMetadata as BasePropertyMetadata;

class PropertyMetadata extends BasePropertyMetadata
{
    public $staticData;

    public $fakerName;
    public $fakerResolverType;
    public $fakerResolverArgs;

    public $ignore = false;
}