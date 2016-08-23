# Property Metadata

Often you won't want to use the exact data in your entities when generating fixtures. For example, let's say you're generating fixtures for a User in your system - you wouldn't want to dump their actual password hash in a generated fixture. You would probably instead like to use some pre-determined test data. This library offers several methods to do this.

## Data

Data allows you to specify an alternate static value that will always be used for a particular property.
 
Annotation:
```php
<?php
use Trappar\AliceGenerator\Annotation as Fixture;

class User
{
    /** @Fixture\Data("test@gmail.com") */
    private $email;
}
```

YAML:
```yaml
# User.yml
User:
    email:
        data: 'test@gmail.com'
```

Example generated fixtures:

```yaml
User:
    User-1:
        email: 'test@gmail.com'
```

## Ignore

Sometimes you just never want a particular property in an object to be dumped to the fixtures. Maybe you have a field where a cron automatically updates some derived data on a periodic basis - why put this in a fixture if it's just going to need to be derived again anyway? This is why there is Ignore. When this is specified, neither the property nor a value will appear in generated fixtures.

Annotation:
```php
<?php
use Trappar\AliceGenerator\Annotation as Fixture;

class User
{
    /** @Fixture\Ignore */
    private $daysSinceCreated;
    
    private $username = 'test';
}
```

YAML:
```yaml
# User.yml
User:
    daysSinceCreated:
        ignore: true
```

Example generated fixtures: *note the exclusion of the daysSinceCreated property*
```yaml
User:
    User-1:
        username: test
```

## Faker

Faker allows you to specify a [Faker](https://github.com/fzaninotto/Faker) provider which will be used in place of the actual value of a property. In its simplest form this looks like:

Annotation:
```php
<?php

use Trappar\AliceGenerator\Annotation as Fixture;

class User
{
    /** @Fixture\Faker("username") */
    private $username;
}
```

YAML:
```yaml
# User.yml
User:
    username:
        faker: username
            
```

Example generated fixtures:

```yaml
User:
    User-1:
        username: <username()>
```

In some cases you may wish to specify arguments for the faker provider. For these situations there is a more complex form for Faker:

Annotation:
```php
<?php

use Trappar\AliceGenerator\Annotation as Fixture;

class User
{
    /** @Fixture\Faker("firstName", type="array", arguments={"male"}) */
    private $firstName;
}
```

YAML:
```yaml
# User.yml
User:
    firstName:
        faker:
            name: firstName
            type: array
            arguments: ["male"]
            
```

Example generated fixtures:

```yaml
User:
    User-1:
        firstName: <username("male")>
```

### Built-in Faker Resolver Types
 
#### array

Pass in a static array of arguments for the faker provider. Example:

```php
/** @Fixture\Faker("myFaker", type="array", arguments={1, true, "hello"}) */
private $myProperty;
```

Example snippet from generated fixtures:
```yaml
myProperty: <myFaker(1, true, "hello")>
```

#### value-as-arg

Passes the value of the property as a single argument to the faker provider. No arguments are required for this faker resolver. Example:

```php
/** @Fixture\Faker("myFaker", type="value-as-arg") */
private $myProperty = 'some value';
```

Example snippet from generated fixtures:
```yaml
myProperty: <myFaker("some value")>
```

#### callback

Runs a custom callback to determine the arguments for the faker provider. Arguments for this faker resolver must take one of two possible forms:

   * A string corresponding to the name of a method inside the class the metadata is being defined for.
   * A string classname and a string method corresponding to a callable static method.
   
##### Examples:

*Calling a method in the same class*

Annotation:
```php
<?php

use Trappar\AliceGenerator\Annotation as Fixture;
use Trappar\AliceGenerator\DataStorage\ValueContext;

class User
{
    /** @Fixture\Faker("firstName", type="callback", arguments={"getFirstNameFakerArgs"}) */
    private $firstName;
    
    private $gender = "female";
    
    // Accepting the ValueContext argument is not strictly necessary here since we're not using it
    public function getFirstNameFakerArgs(ValueContext $context)
    {
        return [$this->gender];
    }
}
```

YAML:
```yaml
User:
    firstName:
        faker:
            name: firstName
            type: callback
            arguments: ["getFirstNameFakerArgs"]
            
```

Example generated fixtures:
```yaml
# User.yml
User:
    User-1:
        firstName: <firstName("female")>
            
```

*Calling a static method in a different class*

Annotation:
```php
<?php

namespace MyNamespace;

use Trappar\AliceGenerator\Annotation as Fixture;
use Trappar\AliceGenerator\DataStorage\ValueContext;

class User
{
    /** @Fixture\Faker("myFaker", type="callback", arguments={"MyNamespace\SomeFakerUtils", "getFirstNameFakerArgs"}) */
    private $myProperty = 'default value';
}

class SomeFakerUtils
{
    public static function getFirstNameFakerArgs(ValueContext $context)
    {
        return [$context->getValue(), true];
    }
}

```

YAML:
```yaml
User:
    myProperty:
        faker:
            name: myFaker
            type: callback
            arguments: ["MyNamespace\SomeFakerUtils", "getFirstNameFakerArgs"]
            
```

Example generated fixtures:
```yaml
# User.yml
User:
    User-1:
        myProperty: <myFaker("default value", true)>
            
```

[Back to Table of Contents](/README.md#table-of-contents)