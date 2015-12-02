<?php

use Phalcon\Mvc\Application;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\DI\FactoryDefault;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Loader;

error_reporting(E_ALL);

try {
    /**
     * Handle the request
     */
    $application = new Application();
    /**
     * Assign the DI
     */
    $application->setDI(setDi());
    require __DIR__ . '/../config/modules.php';
    echo $application->handle()->getContent();

} catch (Phalcon\Exception $e) {
    echo $e->getMessage();
} catch (PDOException $e) {
    echo $e->getMessage();
}

function setDi()
{
    $di = new FactoryDefault();

    $di['router'] = function() use($di)
    {

        $router = new Router();
        $router->setDefaultModule('admin');

        return $router;
    };

    $di['url'] = function()
    {
        $url = new UrlResolver();
        $url->setBaseUri('/');

        return $url;
    };

    $di['session'] = function()
    {
        $session = new SessionAdapter();
        $session->start();

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
    return $di;
}