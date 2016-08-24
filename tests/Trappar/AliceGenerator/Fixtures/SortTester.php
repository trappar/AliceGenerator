<?php

namespace Trappar\AliceGenerator\Tests\Fixtures;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class SortTester
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
    public $ensureNotIgnored;

    /**
     * @var SortTester
     * @ORM\ManyToOne(targetEntity="SortTester")
     */
    public $related;

    public function __construct()
    {
        $this->ensureNotIgnored = uniqid();
    }
}

