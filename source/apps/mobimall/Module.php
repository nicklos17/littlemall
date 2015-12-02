<?php

namespace Mall\MobiMall;

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
            'Mall\MobiMall\Controllers' => __DIR__ . '/controllers/',
            'Mall\Utils' => __DIR__ . '/../utils'
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
        $di->set('dispatcher', function(){
            $eventsManager = new EventsManager();
            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setEventsManager($eventsManager);

            return $dispatcher;
        });

        /**
         * Setting up the view component
         */
        $di['view'] = function ()
        {
            $view = new View();
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
                    \PDO::ATTR_EMULATE_PREPARES => false, //mysql 预处理 不由本地处理 拼接字符串
                    //\PDO::ATTR_STRINGIFY_FETCHES => false, //fetch 数据时将int 也转为string
                )
            ));
        };

        $di->set('dispatcher', function(){
            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setDefaultNamespace("Mall\MobiMall\Controllers");
            return $dispatcher;
        });

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
    }

}
