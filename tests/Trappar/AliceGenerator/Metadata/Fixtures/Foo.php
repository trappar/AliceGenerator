<?php

namespace Trappar\AliceGenerator\Tests\Metadata\Fixtures;

use Trappar\AliceGenerator\Annotation as Fixture;

class Foo
{
    /**
     * @Fixture\Data("test")
     */
    public $staticData;

    /**
     * @Fixture\Faker(name="test", type="array", arguments={"test"})
     */
    public $faker;

    /**
     * @Fixture\Ignore()
     */
    public $ignored;
}