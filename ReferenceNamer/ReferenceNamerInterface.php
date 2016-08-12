<?php

namespace Trappar\AliceGenerator\ReferenceNamer;

interface ReferenceNamerInterface
{
    /**
     * @param $object
     * @return string
     */
    public function createPrefix($object);
}