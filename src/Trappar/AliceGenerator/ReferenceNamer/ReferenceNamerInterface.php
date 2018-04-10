<?php

namespace Trappar\AliceGenerator\ReferenceNamer;

interface ReferenceNamerInterface
{
    /**
     * @param object $object the generated object
     * @param int $key a unique index for all generated objects of the class of $object
     * @return string
     */
    public function createReference($object, $key);
}