<?php

namespace Trappar\AliceGenerator\Metadata\Resolver\Faker;

use Trappar\AliceGenerator\DataStorage\ValueContext;

class ArrayFakerResolver extends AbstractFakerResolver
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'array';
    }

    /**
     * @inheritdoc
     */
    public function handle(ValueContext $valueContext)
    {
        return !is_array($args = $valueContext->getMetadata()->fakerResolverArgs)
            ? [$args]
            : $args;
    }
}