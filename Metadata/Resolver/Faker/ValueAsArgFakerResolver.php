<?php

namespace Trappar\AliceGenerator\Metadata\Resolver\Faker;

use Trappar\AliceGenerator\DataStorage\ValueContext;

class ValueAsArgFakerResolver extends AbstractFakerResolver
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'value-as-arg';
    }

    /**
     * @inheritdoc
     */
    public function handle(ValueContext $valueContext)
    {
        return [$valueContext->getValue()];
    }
}