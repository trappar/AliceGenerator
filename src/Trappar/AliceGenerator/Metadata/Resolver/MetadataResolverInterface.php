<?php

namespace Trappar\AliceGenerator\Metadata\Resolver;

use Trappar\AliceGenerator\DataStorage\ValueContext;

interface MetadataResolverInterface
{
    /**
     * Calls validate, handle, and then saves the resulting value into the ValueContext
     *
     * @param ValueContext $valueContext
     * @return mixed
     */
    public function resolve(ValueContext $valueContext);

    /**
     * Should throw an error if the incoming ValueContext is incompatible with this resolver
     *
     * @param ValueContext $valueContext
     * @return mixed
     */
    public function validate(ValueContext $valueContext);

    /**
     * Uses data in the ValueContext
     *
     * @param ValueContext $valueContext
     * @return mixed
     */
    public function handle(ValueContext $valueContext);
}