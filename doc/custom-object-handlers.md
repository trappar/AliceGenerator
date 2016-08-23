# Custom Object Handlers

There are cases where objects may have non-persisted objects exposed through their properties. For example, when using Doctrine, the datetime type loads data from the database directly into `\DateTime` objects inside your Entities. In these cases the data from the entity objects can't directly be used to create an Alice fixture file.

One solution this library offers for this issue is to make use of [Property Metadata](metadata.md), but this solution can be a bit tedious when you have an object type returned from many places in your database, and you would like it to be handled the same everywhere. 

This is why this library offers a custom object handler feature. Create a custom object handler to accept a particular object type, and any time an object of that type is found on an object's property it will use your custom provider code to create the fixture representation of it.

*Side note: Since `\DateTime` objects appear in entities quite often, a custom handler for it is included in this library. You can take a look at the code for that [object handler here](/src/Trappar/AliceGenerator/ObjectHandler/DateTimeHandler.php).*

## Creating a Custom Object Handler

For the following example we'll create a custom object handler that covers converting a PhoneNumber from [giggsey/libphonenumber-for-php](https://github.com/giggsey/libphonenumber-for-php) to a fixture-friendly format, which could be handled with a Custom Faker Data Provider (see [Alice](https://github.com/nelmio/alice) documentation)

```php
<?php

namespace MyApplication\ObjectHandler;

use Trappar\AliceGenerator\ObjectHandler\ObjectHandlerInterface;
use Trappar\AliceGenerator\DataStorage\ValueContext;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Trappar\AliceGenerator\Faker\FakerGenerator;

class PhoneNumberHandler implements ObjectHandlerInterface
{
    public function handle(ValueContext $valueContext)
    {
        if (!($phoneNumber = $valueContext->getValue()) instanceof PhoneNumber) {
            return false;
        }
        
        $number = PhoneNumberUtil::getInstance()->format($phoneNumber, PhoneNumberFormat::E164);
        
        // Here is one method of creating the fixture representation
        $valueContext->setValue("<phoneNumber('$number')>");
        
        // Or you could also use this utility method, which accomplishes the same goal
        $valueContext->setValue(FakerGenerator::generate('phoneNumber', array($number)));
        
        return true;
    }
}
```

There are a number of important points to pay attention to here:

* When a handle method returns false the ObjectHandlerRegistry continues running other object handlers until either one returns true, or all object handlers have been called.
* It is up to you to call `$valueContext->setValue()` with a value which can be imported when fixtures are loaded. Failing to do so will leave you unable to load the resulting fixtures.
* You may have multiple ObjectHandlers which support the same object type, or that use additional checks to see if the object should be handled, but only one object handler will actually process the object (assuming you properly return true from that handler).

[Back to Table of Contents](/README.md#table-of-contents)