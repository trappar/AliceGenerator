<?php

namespace Trappar\AliceGenerator\Metadata\Resolver\Faker;

use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\Exception\FakerResolverException;

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
        $args = $valueContext->getMetadata()->fakerResolverArgs;

        $target = isset($args[0]) ? $args[0] : null;

        if (count($parts = explode('::', $target)) == 2) {
            list($target, $method) = $parts;
        } else {
            $method = isset($args[1]) ? $args[1] : 'toFixture';
        }

        if ($target == 'self') {
            $target = get_class($valueContext->getContextObject());
        }

        return [$target, $method];
    }
}