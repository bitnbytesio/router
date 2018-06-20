Router - Simple/Fast request router for PHP7
=======================================

Install
-------

To install with composer:

```sh
composer require artisangang/router
```

Requires PHP 7.

Usage
-----

```php
<?php

require '/path/to/vendor/autoload.php';

// storage for locked routes
$storage = new \Router\FileStorage(__DIR__);

$routeManager = new \Router\Manager;
$routeManager->storage( $storage );

// lock routes in production mode for performance
$routeManager->lock();

if (isset($_ENV["development"])) {
    $routeManager->unlock();
}

//product routes
$p = $routeManager->router();
$p->use('GET /product/', 'IndexController@productIndex');
$p->use('GET /product/:id', 'IndexController@productSingle');
$p->use('POST /product', 'IndexController.productCreate');
$p->use('PUT /product/:id', 'IndexController.productUpdate');
$p->use('PATCH /product/:id', 'IndexController.productPatch');
$p->use('DELETE /product/:id', 'IndexController.productDelete');

// category routes
$c = $routeManager->router();
$c->use('GET /category', 'IndexController.categoryIndex');
$c->use('POST /category', 'IndexController.categoryCreate');
$c->use('PUT /category/:id', 'IndexController.categoryUpdate');
$c->use('PATCH /category/:id', 'IndexController.categoryPatch');
$c->use('DELETE /category/:id', 'IndexController.categoryDelete');

// shop routes
$c->get('/shop', 'IndexController.shopIndex');
$c->post('/shop', 'IndexController.shopCreate');
$c->put('/shop/:id', 'IndexController.shopUpdate');
$c->patch('/shop/:id', 'IndexController.shopPatch');
$c->delete('/shop/:id', 'deleteShop');

// group routes in v1 prefix
$r = $routeManager->router('v1');
// add routes to group
$r->child($p, $c);

// create another group with prefic api
$a = $routeManager->router('api');
// add route to this gorup
$a->child($r);

// find route match based on current request uri, this will return Router\Commands\HttpCommand instance if match found
$request = $routeManager->match($a);

if (!$request) {
    die('404,Page not found!');
}

// extract params from uri
// for example with uri /product/1 matched with pattern /product/:id will return ['id' => 1]
$params = $request->params($routeManager->requestUri);


```
