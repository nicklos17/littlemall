<?php

namespace Mall\Mall;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Dispatcher as MvcDispatcher,
    Phalcon\Events\Manager as EventsManager,
    Phalcon\Mvc\Dispatcher\Exception as DispatchException;

class Module implements ModuleDefinitionInterface
{
    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders(\Phalcon\DiInterface $di = NULL)
    {

        $loader = new Loader();

        $loader->registerNamespaces(array(
            'Mall\Mall\Controllers' => __DIR__ . '/controllers/',
            'Mall\Utils' => __DIR__ . '/../utils',
        ));

        $loader->register();
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $di
     */
    public function registerServices(\Phalcon\DiInterface $di = NULL)
    {

        /**
         * Read configuration
         */
        $config = include __DIR__ . "/config/config.php";
        $authConfig = include __DIR__ . "/config/authConfig.php";

        /**
         * Setting up the view component
         */
        $di->set('dispatcher', function () {
            $eventsManager = new EventsManager();
            $eventsManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception)
            {

                //Handle 404 exceptions
                if ($exception instanceof DispatchException) 
                {
                    $dispatcher->forward(array(
                        'controller' => 'public',
                        'action' => 'error404'
                    ));
                    return false;
                }

                //Alternative way, controller or action doesn't exist
                if($event->getType() == 'beforeException')
                {
                    switch ($exception->getCode())
                    {
                        case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                        case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                            $dispatcher->forward(array(
                                'controller' => 'public',
                                'action' => 'error404'
                            ));
                            return false;
                    }
                }
            });

            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setEventsManager($eventsManager);
            $dispatcher->setDefaultNamespace("Mall\Mall\Controllers");

            return $dispatcher;
        });

        $di['view'] = function ()
        {
            $view = new View();
            $view->setViewsDir(__DIR__ . '/views/');

            return $view;
        };

        /**
         * Database connection is created based in the parameters defined in the configuration file
         */
        $di['db'] = function() use($config)
        {
            return new Mysql(array(
                "host" => $config->database->host,
                "username" => $config->database->username,
                "password" => $config->database->password,
                "dbname" => $config->database->dbname,
                "options" => array(
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                    \PDO::ATTR_CASE => \PDO::CASE_LOWER,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    //\PDO::ATTR_STRINGIFY_FETCHES => false,
                )
            ));
        };

        $di->set('cookies', function()
        {
            $cookies = new \Phalcon\Http\Response\Cookies();
            $cookies->useEncryption(false);
            return $cookies;
        });

        $di->set('authConfig', function() use($authConfig)
        {
            return $authConfig;
        });

        $di->set('casLoginUrl', function()
        {
            $config = include __DIR__ . "/../utils/cas/config/webpcConfig.php";
            $backurl = $config['domain'] . '/index/vali?forward=' . $config['domain'] . $_SERVER['REQUEST_URI'];
            return $config['loginUrl'] . '?siteid=' . $config['siteid'] . '&backurl=' . urlencode($backurl);
        });
    }

}
