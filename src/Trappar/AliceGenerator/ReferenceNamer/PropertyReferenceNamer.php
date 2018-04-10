<?php

namespace Trappar\AliceGenerator\ReferenceNamer;

use Doctrine\Common\Util\ClassUtils;

/**
 * Names by the value of a property from the generated object
 *
 * if class of object is not mapped to a property, the behaviour is similar to ClassNamer
 */
class PropertyReferenceNamer implements ReferenceNamerInterface
{
    /**
     * PropertyNames mappings
     *
     * Maps the className of an generated object to its unique property-value to be used
     * @var array
     */
    protected $propertyNames;

    /**
     * PropertyReferenceNamer constructor.
     *
     * @param array $propertyNames should be an array following the shape of [$class => $propertyName]
     */
    public function __construct(array $propertyNames)
    {
        $this->propertyNames = $propertyNames;
    }

    public function createReference($object, $key)
    {
        $class = ClassUtils::getClass($object);

        $parts     = explode('\\', $class);
        $className = $parts[count($parts) - 1];

        if (array_key_exists($class,$this->propertyNames)) {
            $propertyName = $this->propertyNames[$class];
            return $className.'-'.$object->$propertyName;
        } else {
            return $className.'-'.$key;
        }
    }
}