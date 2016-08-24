<?php

namespace Trappar\AliceGenerator\Tests\Fixtures;

use Doctrine\ORM\Mapping as ORM;
use Trappar\AliceGenerator\Annotation as Fixture;

/**
 * @ORM\Entity()
 */
class DoctrinePersisterTester
{
    /**
     * @ORM\Column()
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    public $id;

    /**
     * @ORM\Column()
     */
    public $mappedProperty;

    public $unmappedProperty;
}

