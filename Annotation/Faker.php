<?php

namespace Trappar\AliceGenerator\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Faker implements FixtureAnnotationInterface
{
    /**
     * @Required()
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $arguments;
}