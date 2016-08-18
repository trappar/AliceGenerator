<?php

namespace Trappar\AliceGenerator\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class Data implements FixtureAnnotationInterface
{
    /**
     * @Required()
     * @var string
     */
    public $value;
}