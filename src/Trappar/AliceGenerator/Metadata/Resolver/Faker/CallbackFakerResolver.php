<?php

namespace Trappar\AliceGenerator\Metadata\Resolver\Faker;

use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\Exception\FakerResolverException;
use Trappar\AliceGenerator\Exception\InvalidArgumentException;

class CallbackFakerResolver extends AbstractFakerResolver
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'callback';
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

        switch ($count = count($args)) {
            case 1:
                return [get_class($valueContext->getContextObject()), $args[0]];
            case 2:
                return $args;
        }

        throw new InvalidArgumentException(sprintf(
            'CallbackFakerResolver can accept only one or two arguments, %s given.',
            $count
        ));
    }
}