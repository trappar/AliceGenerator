<?php

namespace Trappar\AliceGenerator\Tests;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Trappar\AliceGenerator\Exception\UnknownObjectTypeException;
use Trappar\AliceGenerator\FixtureGenerationContext;
use Trappar\AliceGenerator\FixtureGeneratorBuilder;
use Trappar\AliceGenerator\Persister\NonSpecificPersister;
use Trappar\AliceGenerator\ReferenceNamer\NamespaceNamer;
use Trappar\AliceGenerator\ReferenceNamer\PropertyReferenceNamer;
use Trappar\AliceGenerator\Tests\Fixtures\ObjectWithConstructor;
use Trappar\AliceGenerator\Tests\Fixtures\Post;
use Trappar\AliceGenerator\Tests\Fixtures\SortTester;
use Trappar\AliceGenerator\Tests\Fixtures\User;
use Trappar\AliceGenerator\Tests\Util\FixtureUtils;
use Trappar\AliceGenerator\YamlWriter;

class FixtureGeneratorTest extends TestCase
{
    public function testDefaultFixtureGenerator()
    {
        $fg = FixtureGeneratorBuilder::create()->build();

        $obj      = new TestObject();
        $obj->foo = 'bar';

        $result = $fg->generateArray($obj);
        $this->assertSame([
            TestObject::class => [
                'TestObject-1' => [
                    'foo' => 'bar'
                ]
            ]
        ], $result);
    }

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

    public function testWithUnknownObjectType()
    {
        $user           = new User();
        $user->username = new \Exception();

        $this->expectException(UnknownObjectTypeException::class);
        FixtureUtils::getFixturesFromObjects($user);
    }

    public function testGenerateYaml()
    {
        $post        = new Post();
        $post->title = 'test';

        $fg   = FixtureUtils::buildFixtureGenerator();
        $yaml = $fg->generateYaml($post);

        $this->assertSame(
            sprintf("%s:\n    Post-1:\n        title: test\n", Post::class),
            $yaml
        );
    }

    public function testGenerateYamlCustomSpacing()
    {
        $post        = new Post();
        $post->title = 'test';

        $fgBuilder = FixtureUtils::buildFixtureGeneratorBuilder([]);
        $fgBuilder->setYamlWriter(new YamlWriter(1, 1));
        $fg   = $fgBuilder->build();
        $yaml = $fg->generateYaml($post);

        $this->assertSame(
            sprintf("%s: { Post-1: { title: test } }\n", Post::class),
            $yaml
        );
    }

    public function testWithoutDoctrine()
    {
        $fgBuilder = FixtureUtils::buildFixtureGeneratorBuilder([]);
        $fgBuilder->setPersister(new NonSpecificPersister());
        $fg = $fgBuilder->build();

        $post        = new Post();
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

        $this->assertArrayHasKey('TrapparAliceGeneratorTestsFixturesUser-1', $results[User::class]);
    }

    public function testUniqueReferenceNamer()
    {
        $user = $this->createTestData();

        $results = FixtureUtils::getFixturesFromObjects(
            $user,
            FixtureGenerationContext::create()->setReferenceNamer(new PropertyReferenceNamer([User::class => 'username']))
        );

        $this->assertArrayHasKey('User-testUser', $results[User::class]);
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

    public function testIgnore()
    {
        $user            = new User();
        $user->username  = 'test';
        $user->lastLogin = 'something';

        $results = FixtureUtils::getFixturesFromObjects($user);

        $this->assertArrayNotHasKey('lastLogin', $results[User::class]['User-1']);
    }

    public function testIgnoreOnRelation()
    {
        $post       = new Post();
        $post->body = 'test';

        $relatedPost       = new Post();
        $relatedPost->body = 'test';
        $post->relatedPost = $relatedPost;

        $results = FixtureUtils::getFixturesFromObjects($post);

        $this->assertCount(1, $results[Post::class]);
    }

    public function testSortReferences()
    {
        $user            = new User();
        $tester          = new SortTester();
        $tester->related = new SortTester();

        $unsortedResults = FixtureUtils::getFixturesFromObjects(
            [$user, $tester],
            FixtureGenerationContext::create()
                ->setSortResults(false)
        );

        $sortedResults = FixtureUtils::getFixturesFromObjects(
            [$user, $tester],
            FixtureGenerationContext::create()
                ->setSortResults(true)
        );

        $this->assertSame([User::class, SortTester::class], array_keys($unsortedResults));
        $this->assertSame([SortTester::class, User::class], array_keys($sortedResults));
        $this->assertSame(['SortTester-2', 'SortTester-1'], array_keys($unsortedResults[SortTester::class]));
        $this->assertSame(['SortTester-1', 'SortTester-2'], array_keys($sortedResults[SortTester::class]));
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

    public function testNotExcludingDefaultValues()
    {
        $results = FixtureUtils::getFixturesFromObjects(
            new User(),
            FixtureGenerationContext::create()
                ->setExcludeDefaultValues(false)
        );

        $this->assertCount(1, $results);
        $this->assertArrayHasKey('username', $results[User::class]['User-1']);
    }

    public function testOnObjectWithConstructor()
    {
        $this->assertCount(
            1,
            FixtureUtils::getFixturesFromObjects(
                new ObjectWithConstructor('test')
            )
        );
    }

    public function testStrictTypeChecking()
    {
        $post = new Post();
        $post->body = 0;

        $builder = FixtureUtils::buildFixtureGeneratorBuilder(false);

        $this->assertCount(
            1,
            $builder->setStrictTypeChecking(true)->build()->generateArray($post)
        );

        $this->assertCount(
            0,
            $builder->setStrictTypeChecking(false)->build()->generateArray($post)
        );
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

class TestObject
{
    public $foo;
}