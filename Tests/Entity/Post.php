<?php

namespace Trappar\AliceGenerator\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Trappar\AliceGenerator\Annotation as Fixture;

/**
 * @ORM\Entity()
 */
class Post
{
    /**
     * @ORM\Column()
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    public $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    public $title;

    /**
     * @ORM\Column(name="body", type="text")
     */
    public $body;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts")
     */
    public $postedBy;
}

