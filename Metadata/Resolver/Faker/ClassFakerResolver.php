<?php

namespace Trappar\AliceGenerator\Metadata\Resolver\Faker;

use Trappar\AliceGenerator\Exception\FakerResolverException;
use Trappar\AliceGenerator\DataStorage\ValueContext;

class ClassFakerResolver extends AbstractFakerResolver
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'class';
    }

    public function validate(ValueContext $valueContext)
    {
        $target = $this->getTarget($valueContext);

        if (!is_callable($target)) {
            list($class, $method) = $target;

            throw new FakerResolverException($valueContext, sprintf(
                'supplied method must be callable, "%s::%s" given.',
                $class, $method
            ));
        }
    }

    /**
     * @inheritdoc
     */
    public function handle(ValueContext $valueContext)
    {
        return call_user_func($this->getTarget($valueContext), $valueContext);
    }

    private function getTarget(ValueContext $valueContext)
    {
        $args   = $valueContext->getMetadata()->fakerResolverArgs;
        $target = isset($args[0]) ? $args[0] : null;
        $method = isset($args[1]) ? $args[1] : 'toFixture';

        return [$target, $method];
    }
}