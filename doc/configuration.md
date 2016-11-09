# Configuration

#### Important!

***If you are using Symfony/Doctrine, this section is mostly irrelevant for you as the entire integration is provided by using [AliceGeneratorBundle](https://github.com/trappar/AliceGeneratorBundle). If you are using another framework, there also might be a module, or other special integration. Please check packagist, or whatever registry usually holds such information for your framework.***

## Constructing a FixtureGenerator

This library provides a special builder object which makes constructing `FixtureGenerator` instances a breeze in any PHP project. In its shortest version, it’s just a single line of code:

```php
<?php
$fixtureGenerator = \Trappar\AliceGenerator\FixtureGeneratorBuilder::create()->build();
```

## Using with Doctrine

The `FixtureGenerator` configured above will work fine with any object and no special configuration, but it also has the ability to be configured to work better with Doctrine:

```php
<?php
// Create a basic Doctrine EntityManager
$em = \Doctrine\ORM\EntityManager::create(array(
    'driver' => 'pdo_sqlite',
    'path'   => __DIR__ . '/db.sqlite',
), \Doctrine\ORM\Tools\Setup::createConfiguration(true));

$fixtureGenerator = \Trappar\AliceGenerator\FixtureGeneratorBuilder::create()
    ->setPersister(
        new \Trappar\AliceGenerator\Persister\DoctrinePersister($em)
    )
    ->build();
```

The `DoctrinePersister` affects FixtureGeneration in a number of ways:

   * Prevents ID and unmapped properties from being saved into fixtures
   * Handles Doctrine proxy objects
   * Forces the FixtureGenerator to only save Doctrine Entities (other objects can be handled in other ways)

## Adding Custom Object Handlers

See [Custom Object Handlers](custom-object-handlers.md)

If you have created custom Object Handlers you can add them to the `FixtureGenerator`:

```php
<?php
use Trappar\AliceGenerator\FixtureGeneratorBuilder;
use Trappar\AliceGenerator\ObjectHandlerRegistry;

$fixtureGenerator = FixtureGeneratorBuilder::create()
    ->addDefaultObjectHandlers()
    ->configureObjectHandlerRegistry(function(ObjectHandlerRegistry $registry){
        $registry->registerHandler(new YourCustomHandler());
    })
    ->build();
```

#### Important notes:

   * Calling `addDefaultObjectHandlers()` isn't strictly necessary, but not doing so will result in losing the default ObjectHandlers, which includes support for Doctrine `Collection` and `DateTime`.
   * Handlers are called in reverse order, meaning that if you add a second handler which can operate on `DateTime` objects after the default `DateTimeHandler` has already been added **your object handler will be called first**.

## Configuring Metadata Locations

This library supports two metadata sources. By default, it uses annotations (via Doctrine common annotations), but you may also store metadata in YML files. For the latter, it is necessary to configure a metadata directory where those files are located:

```php
<?php
$fixtureGenerator = \Trappar\AliceGenerator\FixtureGeneratorBuilder::create()
    ->addMetadataDir(__DIR__ . '/my-dir', 'MyApplication\Entity')
    ->build();
```

`addMetadataDir()` takes two arguments - a directory and a prefix.

The namespace prefix will make the names of the actual metadata files a bit shorter. For example, let's assume
that you have a directory where you only store metadata files for the `MyApplication\Entity` namespace.

If you use an empty prefix, your metadata files would need to look like:

   * `my-dir/MyApplication.Entity.SomeObject.yml`
   * `my-dir/MyApplication.Entity.OtherObject.xml`

If you use `MyApplication\Entity` as prefix, your metadata files would need to look like:

   * `my-dir/SomeObject.yml`
   * `my-dir/OtherObject.yml`

Please keep in mind that you currently may only have **one directory per namespace prefix.** If you add a namespace prefix which already exists it will simply overwrite the previous directory configured for that prefix.

## Disabling Strict Type Checking

By default this library compares each value found in objects passed into it against the default value for that property. This comparison is done using the identical operator (===). By turning off strict type checking the `FixtureGenerator` will instead use the equal operator (==) whenever the value is **not** a null, bool, or object. This can be helpful if your schema contains certain situations, like when properties default as false but are saved in the database as either 0/1.

`setStrictTypeChecking( bool $enabled )` - Defaults to true.

[Back to Table of Contents](/README.md#table-of-contents)