<?php

namespace Trappar\AliceGenerator\Tests\PropertyNamer;

use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\DataStorage\ValueContext;
use Trappar\AliceGenerator\Metadata\PropertyMetadata;
use Trappar\AliceGenerator\PropertyNamer\PropertyNamer;
use Trappar\AliceGenerator\Tests\Fixtures\Post;

class PropertyNamerTest extends TestCase
{
    public function testCreateNamer()
    {
        $object        = new Post();
        $object->title = 'New Post';
        $metadata      = new PropertyMetadata(Post::class, 'title');

        $valueContext  = new ValueContext($object->title, Post::class, $object, $metadata);

        $this->assertEquals(
            'title',
            $this->getNamer()->createName($valueContext)
        );
    }

    private function getNamer()
    {
        return new PropertyNamer();
    }
}
