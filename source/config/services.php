<?php

/**
 * Services are globally registered in this file
 */

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\DI\FactoryDefault;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Loader;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * Registering a router
 */
$di['router'] = function() use($di)
{

    $router = new Router();
    
    $router->setDefaultModule('mall');

    return $router;
};

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di['url'] = function()
{
    $url = new UrlResolver();
    $url->setBaseUri('/');

    return $url;
};

/**
 * Start the session the first time some component request the session service
 */
$di['session'] = function()
{
    $session = new SessionAdapter();
    if (!isset($_SESSION))
    {
        $session->start();
    }

    return $session;
};

$loader = new Loader();
$loader->registerNamespaces(array(
    'Mall\Mdu' => __DIR__ . '/../apps/mdu'
));
$sysConfig=include __DIR__ . '/../config/sysconfig.php';
$di['sysconfig'] = function() use ($sysConfig)
{
    return $sysConfig;
};

$loader->register();

