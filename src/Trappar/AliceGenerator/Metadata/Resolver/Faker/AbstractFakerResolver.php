<?php

namespace Trappar\AliceGenerator\Metadata\Resolver\Faker;

use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\Faker\FakerGenerator;
use Trappar\AliceGenerator\Metadata\Resolver\AbstractMetadataResolver;

abstract class AbstractFakerResolver extends AbstractMetadataResolver implements FakerResolverInterface
{
    final public function resolve(ValueContext $valueContext)
    {
        $this->validate($valueContext);

        $result = $this->handle($valueContext);

        if (!$result instanceof ValueContext) {
            if (is_array($result)) {
                $result = FakerGenerator::generate($valueContext->getMetadata()->fakerName, $result);
            }
            $valueContext->setValue($result);
        }
    }

    /**
     * @inheritDoc
     */
    public function validate(ValueContext $valueContext)
    {
    }
}