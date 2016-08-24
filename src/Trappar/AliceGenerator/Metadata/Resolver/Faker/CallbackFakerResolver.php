<?php

namespace Trappar\AliceGenerator\Metadata\Resolver\Faker;

use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\Exception\FakerResolverException;

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

        if ($target === false) {
            throw new FakerResolverException($valueContext, sprintf(
                'CallbackFakerResolver can only accept one or two arguments.'
            ));
        } elseif (is_array($target)) {
            if (!is_callable($target)) {
                list($class, $method) = $target;

                throw new FakerResolverException($valueContext, sprintf(
                    'supplied method must be statically callable, "%s::%s" given.',
                    $class, $method
                ));
            }
        } else {
            if (!method_exists($contextObject = $valueContext->getContextObject(), $target)) {
                throw new FakerResolverException($valueContext, sprintf(
                    'method "%s" must publicly exist in "%s".',
                    $target, $valueContext->getContextObjectClass()
                ));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function handle(ValueContext $valueContext)
    {
        $target = $this->getTarget($valueContext);
        if (is_string($target)) {
            $target = [$valueContext->getContextObject(), $target];
        }

        return call_user_func($target, $valueContext);
    }

    private function getTarget(ValueContext $valueContext)
    {
        switch ($count = count($args = $valueContext->getMetadata()->fakerResolverArgs)) {
            case 1:
                return $args[0];
            case 2:
                return $args;
            default:
                return false;
        }
    }
}