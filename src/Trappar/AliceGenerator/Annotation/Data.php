<?php

namespace Trappar\AliceGenerator\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;

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