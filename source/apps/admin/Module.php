<?php

namespace Mall\Admin;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Db\Adapter\Pdo\Mysql;

class Module implements ModuleDefinitionInterface
{

    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders(\Phalcon\DiInterface $di = NULL)
    {

        $loader = new Loader();

        $loader->registerNamespaces(array(
            'Mall\Admin\Controllers' => __DIR__ . '/controllers/',
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
        /**
         * Setting up the view component
         */
        $di['view'] = function ()
        {
            $view = new View();
            $view->setViewsDir(__DIR__ . '/views/');

            return $view;
        };

        /**
         * Database connection is created based in the parameters defined in the configuration file
         */
        $di['db'] = function () use ($config)
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

        $di->set('dispatcher', function(){
            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setDefaultNamespace("Mall\Admin\Controllers");
            return $dispatcher;
        });

        $di->set('cookies', function()
        {
            $cookies = new \Phalcon\Http\Response\Cookies();
            $cookies->useEncryption(false);
            return $cookies;
        });
    }
}
