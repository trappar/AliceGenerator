<?php

namespace Trappar\AliceGenerator\Metadata\Resolver;

use Trappar\AliceGenerator\DataStorage\ValueContext;

abstract class AbstractMetadataResolver implements MetadataResolverInterface
{
    public function resolve(ValueContext $valueContext)
    {
        $this->validate($valueContext);
        $this->handle($valueContext);
    }
}