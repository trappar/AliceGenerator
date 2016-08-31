<?php

namespace Trappar\AliceGenerator\Tests\Fixtures;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ObjectWithConstructor
{
    /**
     * @ORM\Column()
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @ORM\Column()
     */
    private $foo;

    public function __construct($fooValue)
    {
        $this->foo = $fooValue;
    }
}
