<?php

namespace Trappar\AliceGenerator\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Faker implements FixtureAnnotationInterface
{
    /**
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