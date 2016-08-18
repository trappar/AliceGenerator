<?php

namespace Trappar\AliceGenerator\Metadata\Resolver\Faker;

use Trappar\AliceGenerator\DataStorage\ValueContext;

class NoArgFakerResolver extends AbstractFakerResolver
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function handle(ValueContext $valueContext)
    {
        return [];
    }
}