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
```php
<?php

$container = new Container;

$container
    ->bind('db', 'PDO')
    ->asSingleton()
    ->bindParameter('dsn', 'mysql:host=localhost')
    ->bindParameter('username', 'root')
    ->bindParameter('passwd', '');

if ($container->has('db')) {
    $db = $container->get('db');
}
```
