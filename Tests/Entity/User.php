<?php

namespace Trappar\AliceGenerator\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Trappar\AliceGenerator\Annotation as Fixture;

/**
 * @ORM\Entity()
 */
class User
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
    public $username;

    /**
     * @ORM\Column()
     * @Fixture\Data("test")
     */
    public $password;

    /**
     * @ORM\Column()
     * @Fixture\Faker("name", type="array", arguments={"male"})
     */
    public $name;

    /**
     * @ORM\Column()
     * @Fixture\Faker("email")
     */
    public $email;

    /**
     * @var array
     * @ORM\Column(name="roles", type="simple_array")
     */
    public $roles = ['ROLE_USER'];
    
    /**
     * @var Post[]
     * @ORM\OneToMany(targetEntity="Post", mappedBy="postedBy", cascade={"persist"})
     */
    public $posts;

    /**
     * @ORM\Column()
     * @Fixture\Ignore()
     */
    public $lastLogin;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }
}

