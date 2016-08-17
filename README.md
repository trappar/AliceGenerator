AliceGenerator [![Build Status](https://travis-ci.org/trappar/AliceGenerator.svg?branch=master)](https://travis-ci.org/trappar/AliceGenerator)
===========

Recursively convert existing objects into [Alice](https://github.com/nelmio/alice) Fixtures.

## Documentation

1. [Why?](#why)
    1. [Example](#example)
2. [Installation](#installation)
3. [Basic usage](#basic-usage)
4. [Resources](#resources)

## Why?

Sometimes you find yourself working on a large project with no existing fixtures.
In this case even though Alice makes fixtures much easier to write, that process can still be tedious.

This library proposes an alternate starting point - *automatically generate fixtures from your existing data.*

This opens up a whole new, much faster way to get your test data established... just enter it in your user interface!

#### Example

Let's say you have the following objects

```php
<?php

class Post
{
    public $title;
    public $body;
    public $postedBy;
}

class User
{
    public $username;
}

$user = new User();
$user->username = 'Jeff';

$post1 = new Post();
$post1->title = 'Is Making Fixtures Too Time Consuming?';
$post1->body = 'Check out Alice!';
$post1->postedBy = $user;

$post2 = new Post();
$post2->title = 'Too Much Data to Hand Write?';
$post2->body = 'Check out AliceGenerator!';
$post2->postedBy = $user;
```

This library let's you turn that directly into...

```yaml
Post:
    Post-1:
        title: 'Is Making Fixtures Too Time Consuming?'
        body: 'Check out Alice!'
        postedBy: '@User-1'
    Post-2:
        title: 'Too Much Data to Hand Write?'
        body: 'Check out AliceGenerator!'
        postedBy: '@User-1'
User:
    User-1:
        username: Jeff
```

## Installation

You can use [Composer](https://getcomposer.org/) to install the bundle to your project:

```bash
composer require --dev trappar/alice-generator-bundle
```

## Features

  * Framework support
    * Supports Symfony via [AliceGeneratorBundle](https://github.com/trappar/AliceGeneratorBundle) - Start generating fixtures immediately with *zero custom code required!*
  * ORM support
    * Supports Doctrine natively
    * Can operate without any ORM
    * Can be extended to support any ORM
  * Many ways to make use of Faker providers
  * Configure how your objects are serialized using annotations or YAML metadata
  * Can serialize any object type using custom ObjectHandlers
  * Supports multiple levels of recursion taming
    * Handles circular references automatically
    * Customizable maximum recursion depth
    * Can restrict object traversal to only specific objects of a type
  * Supports several methods of naming Alice references natively, fully customizable

## Basic usage

Using the FixtureGeneratorBuilder to produce a FixtureGenerator

```php
<?php

use Trappar\AliceGenerator\FixtureGeneratorBuilder;

$builder = new FixtureGeneratorBuilder();
$fixtureGenerator = $builder->build();
```

To use Doctrine

```php
<?php

use Trappar\AliceGenerator\FixtureGeneratorBuilder;
use Trappar\AliceGenerator\Persister\DoctrinePersister;

/**
 * We will need an instance of a Doctrine ObjectManager (or EntityManager)
 * @var \Doctrine\Common\Persistence\ObjectManager $om
 */

$builder = new FixtureGeneratorBuilder();
$builder->setPersister(new DoctrinePersister($om));
$fixtureGenerator = $builder->build();
````

Using a FixtureGenerator

```php
<?php

/** @var \Trappar\AliceGenerator\FixtureGenerator $fixtureGenerator */

$obj = new \stdClass();
$obj->myProp = 'test';

$yaml = $fixtureGenerator->generateYaml($obj);

echo $yaml;
```

## Credits

This bundle was developped by [Jeff Way](https://github.com/trappar) with quite a lot of inspiration from:
 * [nelmio/alice](https://github.com/nelmio/alice)
 * [schmittjoh/serializer](https://github.com/schmittjoh/serializer)

[Other contributors](https://github.com/trappar/AliceGeneratorBundle/graphs/contributors).

## License

[![license](https://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](Resources/meta/LICENSE)