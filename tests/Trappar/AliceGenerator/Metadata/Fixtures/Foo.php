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
     * @Fixture\Faker("test", type="array", arguments={"test"})
     */
    public $faker;

    /**
     * @Fixture\Faker("test")
     */
    public $fakerShort;

    /**
     * @Fixture\Ignore()
     */
    public $ignored;
}