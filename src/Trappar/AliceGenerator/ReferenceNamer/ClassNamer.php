<?php

namespace Trappar\AliceGenerator\ReferenceNamer;

use Doctrine\Common\Util\ClassUtils;

class ClassNamer implements ReferenceNamerInterface
{
    public function createReference($object, $key)
    {
        return $this->createPrefix($object).$key;
    }

    public function createPrefix($object)
    {
        $class = ClassUtils::getClass($object);

        $parts     = explode('\\', $class);
        $className = $parts[count($parts) - 1];

        return $className.'-';
    }
}