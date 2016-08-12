<?php

namespace Trappar\AliceGenerator\Exception;

use Trappar\AliceGenerator\DataStorage\ValueContext;

class FakerResolverException extends RuntimeException
{
    public function __construct(ValueContext $valueContext, $message)
    {
        parent::__construct(sprintf(
            'Faker on property "%s" of class "%s" - %s',
            $valueContext->getPropName(),
            $valueContext->getMetadata()->class,
            $message
        ));
    }
}