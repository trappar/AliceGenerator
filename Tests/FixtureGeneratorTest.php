<?php

namespace Trappar\AliceGenerator\Tests;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\FixtureGenerationContext;
use Trappar\AliceGenerator\Persister\NonSpecificPersister;
use Trappar\AliceGenerator\ReferenceNamer\NamespaceNamer;
use Trappar\AliceGenerator\Tests\Entity\Post;
use Trappar\AliceGenerator\Tests\Entity\User;
use Trappar\AliceGenerator\Tests\Util\FixtureUtils;

class FixtureGeneratorTest extends TestCase
{
    public function testMultipleEntities()
    {
        $user = $this->createTestData();

        $fixtures = FixtureUtils::getFixturesFromObjects($user);
        $results  = FixtureUtils::getObjectsFromFixtures($fixtures);

        $this->assertSame('<email()>', $fixtures[User::class]['User-1']['email']);
        $this->assertSame('<name("male")>', $fixtures[User::class]['User-1']['name']);

        /** @var User $processedUser */
        $processedUser = $results['User-1'];

        // Doesn't matter that the resulting user has an array instead of an ArrayCollection - Doctrine will handle it
        $processedUser->posts = new ArrayCollection($processedUser->posts);

        // These were set using Faker so we wouldn't expect them to be the same
        $processedUser->email = null;
        $processedUser->name  = null;

        $this->assertEquals($user, $processedUser);
    }

    public function testGenerateYaml()
    {
        $post = new Post();
        $post->title = 'test';

        $fg = FixtureUtils::buildFixtureGenerator();
        $yaml = $fg->generateYaml($post);

        $this->assertSame(
            sprintf("%s:\n    Post-1:\n        title: test\n", Post::class),
            $yaml
        );
    }

    public function testWithoutDoctrine()
    {
        $fgBuilder = FixtureUtils::buildFixtureGeneratorBuilder([]);
        $fgBuilder->setPersister(new NonSpecificPersister());
        $fg = $fgBuilder->build();

        $post = new Post();
        $post->title = 'test';

        $results = $fg->generateArray($post);

        $this->assertCount(1, $results);
    }

    public function testEntitiesLimitedRecursion()
    {
        $user = $this->createTestData();

        $results = FixtureUtils::convertObjectToFixtureAndBack(
            $user,
            FixtureGenerationContext::create()->setMaximumRecursion(0)
        );

        $this->assertCount(1, $results);
    }

    public function testNamespaceReferenceNamer()
    {
        $user = $this->createTestData();

        $results = FixtureUtils::getFixturesFromObjects(
            $user,
            FixtureGenerationContext::create()->setReferenceNamer(new NamespaceNamer())
        );

        $this->assertArrayHasKey('TrapparAliceGeneratorTestsEntityUser-1', $results[User::class]);
    }

    public function testIgnoringEmptyEntity()
    {
        $user           = new User();
        $user->username = 'test';
        $post           = new Post();
        $user->posts->add($post);
        $user->posts->add($post);

        $this->assertCount(1, FixtureUtils::convertObjectToFixtureAndBack($user));
    }

    public function testEntityConstraints()
    {
        $user1           = new User();
        $user1->username = 'user1';

        $user2           = new User();
        $user2->username = 'user2';

        $results = FixtureUtils::convertObjectToFixtureAndBack(
            [$user1, $user2],
            FixtureGenerationContext::create()
                ->addPersistedObjectConstraint($user1)
        );

        $this->assertCount(1, $results);
        $this->assertSame('user1', current($results)->username);
    }

    private function createTestData()
    {
        $user           = new User();
        $user->username = 'testUser';
        $user->password = 'test';
        $user->roles    = ['ROLE_ADMIN'];

        $post1           = new Post();
        $post1->title    = 'How To Do Something';
        $post1->body     = 'Just do it!';
        $post1->postedBy = $user;

        $post2           = new Post();
        $post2->title    = 'Web Development Made Easy';
        $post2->body     = 'Just do it!';
        $post2->postedBy = $user;

        $user->posts->add($post1);
        $user->posts->add($post2);

        return $user;
    }
}