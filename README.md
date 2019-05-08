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

1. Simple binding:
```php

$container = new Container;

$container->bind('SomeClass');
$container->bind(AnotherClass::class);

//
// Binding a singleton:
//
$container->bind('SomeClass')
    ->asSingleton();

//
// or use singleton method:
//
$container->singleton(AnotherClass::class);

//
// To resolve the instance call get method:
//
if ($container->has('SomeClass')) {
    $instance = $container->get('SomeClass');
}

//
// You can also resolve the instance without binding:
//
$instance = $container->resolve('OneMoreClass');
```

2. Binding a closure:
```php
<?php

$container = new Container;

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

3. Binding an interface:
```php
<?php

$container = new Container;

$container->bind('SomeClass', 'SomeInterface');

//
// Or use an alias name of the interface:
//
$container->bind('logger', '\Psr\Log\LoggerInterface');

//
// Will resolve an instance of \Psr\Log\LoggerInterface:
//
$logger = $container->get('logger');
```

4. Binding parameters:
```php
<?php

class SomeClass
{
    /**
     * @param \AnotherClass $anotherClass
     * @param int           $id
     * @param string        $name
     */
    public function __construct(AnotherClass $anotherClass, int $id, string $name)
    {
        //
    }
}

$container = new Container;

$container->bind('some_class', 'SomeClass')
    ->bindParameter('id', 10)
    ->bindParameter('name', 'Max');

//
// You can pass a parameter collection
// as the second argument of the closure:
//
$container->bind('some_class', function ($container, $parameters) {
    return new SomeClass(
        $container->get('AnotherClass'),
        $parameters->get('id')->getValue(),
        $parameters->get('name')->getValue()
    );
})->bindParameter('id', 10)->bindParameter('name', 'Max');
```
