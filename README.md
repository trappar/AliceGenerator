AliceGenerator [![Build Status](https://travis-ci.org/trappar/AliceGenerator.svg?branch=master)](https://travis-ci.org/trappar/AliceGenerator)
==============

Recursively convert existing objects into [Alice](https://github.com/nelmio/alice) Fixtures.

## Introduction

Sometimes you find yourself working on a large project with no existing fixtures.
In this case even though Alice makes fixtures much easier to write, that process can still be tedious.

This library proposes an alternate starting point - *automatically generate fixtures from your existing data.*

This opens up a whole new, much faster way to get your test data established... just enter it in your user interface!

#### Example

Let's say you have the following objects

```php
<?php
class Post {
    public $title;
    public $body;
    public $postedBy;
}

class User {
    public $username;
}

$user = new User();
$user->username = 'Trappar';

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

You can use [Composer](https://getcomposer.org/) to install the library to your project:

```bash
composer require trappar/alice-generator
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
* Supports several methods of naming Alice references natively - fully customizable

## Table of Contents

* [Usage](doc/usage.md)
  * [Basic usage](doc/usage.md#basic-usage)
  * [Fixture Generation Contexts](doc/usage.md#fixture-generation-contexts)
    * [Limiting Recursion](doc/usage.md#limiting-recursion)
      * [Limiting Recursion Depth](doc/usage.md#limiting-recursion-depth)
      * [Limiting Recursion With Object Constraints](doc/usage.md#limiting-recursion-with-object-constraints)
    * [Customizing Reference Naming Strategy](doc/usage.md#customizing-reference-naming-strategy)
    * [Force Inclusion of Properties at Default Values](doc/usage.md#force-inclusion-of-properties-at-default-values)
* [Property Metadata](doc/metadata.md)
  * [Data](doc/metadata.md#data)
  * [Ignore](doc/metadata.md#ignore)
  * [Faker](doc/metadata.md#faker)
  * [Built-in Faker Resolver Types](doc/metadata.md#built-in-faker-resolver-types)
    * [array](doc/metadata.md#array)
    * [value-as-arg](doc/metadata.md#value-as-arg)
    * [callback](doc/metadata.md#callback)
* [Custom Object Handlers](doc/custom-object-handlers.md)
* [Configuration](doc/configuration.md)
  * [Constructing a FixtureGenerator](doc/configuration.md#constructing-a-fixturegenerator)
  * [Using with Doctrine](doc/configuration.md#using-with-doctrine)
  * [Adding Custom Object Handlers](doc/configuration.md#adding-custom-object-handlers)
  * [Configuring Metadata Locations](doc/configuration.md#configuring-metadata-locations)
  
## Resources

* [Changelog](changelog.md)

## Credits

This bundle was developed by [Jeff Way](https://github.com/trappar) with quite a lot of inspiration from:
 * [nelmio/alice](https://github.com/nelmio/alice)
 * [schmittjoh/serializer](https://github.com/schmittjoh/serializer)

[Other contributors](https://github.com/trappar/AliceGenerator/graphs/contributors).

## License

[![license](https://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](Resources/meta/LICENSE)
