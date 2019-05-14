# Container
The PSR-11 DI Container

## Requirements
1. PHP 7.1.3 or higher

## Installation
```bash
composer require dionchaika/container:dev-master
```

```php
<?php

require_once 'vendor/autoload.php';
```

## Basic usage
1. Configuration:
```php
<?php

use Dionchaika\Container\Container;

$container = new Container;

//
// To use global container
// instance use getInstance method:
//
$container = Container::getInstance();
```

2. Simple binding:
```php
<?php

$container->bind('SomeClass');
$container->bind(AnotherClass::class);

//
// Binding a singleton:
//
$container->bind('SomeClass')->asSingleton();

//
// or use singleton method:
//
$container->singleton(AnotherClass::class);

//
// To bind an entire instance
// you can use instance method:
//
$container->instance('some_instance', new SomeClass);
$container->instance('another_instance', ['foo' => 'bar', 'baz' => 'bat']);

//
// To retrieve an instance call get method:
//
if ($container->has('SomeClass')) {
    $someInstance = $container->get('SomeClass');
}

//
// You can also make an instance without binding:
//
$oneMoreInstance = $container->make('OneMoreClass');
```

3. Binding a closure:
```php
<?php

$container->bind('SomeClass', function () {
    return new SomeClass;
});

//
// You can pass a container instance
// as the first argument of the closure:
//
$container->bind(AnotherClass::class, function ($container) {
    return new AnotherClass($container->get('SomeClass'));
});
```

4. Binding an interface:
```php
<?php

$container->bind('SomeClass', 'SomeInterface');
$container->bind(AnotherClass::class, AnotherInterface::class);

//
// or use an alias name of the interface:
//
$container->bind('logger', '\Psr\Log\LoggerInterface');

//
// Will retrieve an instance of \Psr\Log\LoggerInterface:
//
$logger = $container->get('logger');
```

5. Binding parameters:
```php
<?php

class SomeClass
{
    /**
     * @param \AnotherClass $someInstance
     * @param int           $id
     * @param string        $name
     */
    public function __construct(AnotherClass $someInstance, int $id, string $name)
    {
        //
    }
}

//
// To bind parameters use bindParameter method:
//
$container->bind('some_class', 'SomeClass')
    ->bindParameter('id', 10)
    ->bindParameter('name', 'Max');

//
// You can also pass a closure as the parameter value:
//
$container->instance('name', 'Max');
$container->bind('some_class', 'SomeClass')
    ->bindParameter('id', 10)
    ->bindParameter('name', function ($container) {
        return $container->get('name');
    });

//
// You can pass a parameter collection
// as the second argument of the closure:
//
$container->bind('some_class', function ($container, $parameters) {
    return new SomeClass(
        $container->get(AnotherClass::class),
        $parameters->get('id')->getValue(),
        $parameters->get('name')->getValue()
    );
})->bindParameter('id', 10)->bindParameter('name', 'Max');

//
// You can also pass the array of parameters to bind method:
//
$container->bind('some_class', 'SomeClass', false, [

    'id'   => 10,
    'name' => 'Max'

]);

//
// or make an instance with parameters:
//
$someInstance = $container->make('SomeClass', ['id' => 10, 'name' => 'Max']);
```

6. Calling methods:
```php
<?php

class SomeClass
{
    /**
     * @param int    $id
     * @param string $name
     * @return void
     */
    public function foo(int $id, string $name): void
    {
        //
    }
}

$container->bind('some_class', 'SomeClass');

//
// To invoke a class method
// retrieving arguments use call method:
//
$container->call('some_class', 'foo', ['id' => 10, 'name' => 'Max']);

//
// You can also pass an entire instance to call method:
//
$container->call(new SomeClass, 'foo', ['id' => 10, 'name' => 'Max']);
```

7. Setter injection:
```php
<?php

class SomeClass
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;

    public function __construct()
    {
        //
    }

    //
    // Just define methods starting with "set"
    // and pass class dependencies as its arguments:
    //

    /**
     * @param \Psr\Log\LoggerInterface
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface
     * @return void
     */
    public function setRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }
}

//
// Configure the container for setter injection:
//
$container = new Container(['resolver' => new SetterResolver]);

$someInstance = $container->get('SomeClass');
```

8. Property injection:
```php
<?php

class SomeClass
{
    //
    // Just define a property type in doc comments:
    //

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;

    public function __construct()
    {
        //
    }
}

//
// Configure the container for property injection:
//
$container = new Container(['resolver' => new PropertyResolver]);

$someInstance = $container->get('SomeClass');
```
