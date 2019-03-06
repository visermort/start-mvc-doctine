<?php

namespace app;

use app\classes\ConsoleRunner;

/**
 * Class App
 * @package app
 */
class App
{
    private static $instance;

    private $components = [];

    private $isConsole = false;

    private $controllerName;

    private $actionName;

    private $actionSlug;

    private $actionParams =[];

    private $rootPath;

    protected $controller;

    private function __construct()
    {
    }
    private function __clone()
    {
    }
    private function __wakeup()
    {
    }

    /**run application
     * @return App
     */
    public static function init($web = true)
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        self::$instance->rootPath = realpath(__DIR__ . '/../');

        self::$instance->isConsole = !$web;
        self::$instance->makeComponents();


        $config = self::getComponent('config');
        if ($config->get('app.debug')) {
            ini_set('display_errors', 1);
        }

        //check if user is logged
        self::getComponent('auth');

        if (!self::$instance->isConsole) {
            self::$instance->run();
        }
        return self::$instance;
    }


    /**
     * parse url path and run controller/action($params)
     * run application
     */
    private function run()
    {
        $request = self::getComponent('request');
        $config = self::getComponent('config');
        $dispatcher = $this->getDispatcher($config->getSection('routes'));
        $routeInfo = $dispatcher->dispatch($request->get('method'), $request->get('path'));

        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                $this->errorNotFound();
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $this->error405();
                break;
            case \FastRoute\Dispatcher::FOUND:
                $this->handleAction($routeInfo);
                break;
        }
    }

    /**
     * @param $routesConfig
     * @return \FastRoute\Dispatcher
     */
    private function getDispatcher($routesConfig)
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $route) use ($routesConfig) {
            foreach ($routesConfig as $routeGroupItem) {
                $route->addRoute($routeGroupItem[0], $routeGroupItem[1], $routeGroupItem[2]);
            }
        });
        return $dispatcher;
    }

    /**
     * @param $routeInfo
     */
    private function handleAction($routeInfo)
    {
        $config = App::getComponent('config');
        $auth = App::getComponent('auth');
        $this->actionParams = $routeInfo[2];
        $handler = explode('.', $routeInfo[1]);
        $this->controllerName = 'app\controllers\\' . ucfirst($handler[0]) . 'Controller';
        $controllerFile = ucfirst($handler[0]) . 'Controller.php';
        $this->actionName = 'action' . ucfirst($handler[1]);
        $this->actionSlug = $handler[0] . '.' . $handler[1];
        if (!file_exists($this->rootPath . '/app/controllers/' . $controllerFile) ||
            !method_exists($this->controllerName, $this->actionName)) {
            //there is not a controller file or action name == index
            if ($config->get('app.debug')) {
                echo 'There is not a controller file "' . $controllerFile;
            }
            $this->errorNotFound();
        }
        if (isset($handler[2])) {
            //part of hangler for checking permissions
            if ($handler[2] == 'auth') {
                //need login and not logged
                if ($auth->isGuest()) {
                    $this->redirect($config->get('app.login_url'));
                }
            } else {
                //check permission for $handler[2]
                $auth = self::getComponent('auth');
                $user = $auth->getUser();
                $checkUser = $user ? $user->hasAccessTo($handler[2]) : false;
                if (!$checkUser) {
                    //user does not exists or user does not have a permission
                    $this->error405();
                }
            }
        }
        $this->startAction();
    }

    /**
     * @param $arguments
     * @throws \ReflectionException
     */
    public function runConsole($arguments)
    {
        $consoleRunner = new ConsoleRunner($arguments);
        if ($consoleRunner->actionExists()) {
            $consoleRunner->actionRun();
        } else {
            echo $consoleRunner->getEnabledCommands();
        }
    }

    /**
     * @return mixed
     */
    public static function getRootPath()
    {
        return self::$instance->rootPath;
    }

    /**
     * get component instance
     * @param $name
     * @return mixed
     */
    public static function getComponent($name)
    {
        $name = ucfirst($name);
        $className = 'app\components\\' . $name;
        if (self::$instance->components[$name] === null && class_exists($className)) {
            self::$instance->components[$name] = $className::init();
        }
        return self::$instance->components[$name];
    }

    /**
     * @return bool
     */
    public static function isConsole()
    {
        return self::$instance->isConsole;
    }

    /**
     * @return mixed
     */
    public static function getController()
    {
        return self::$instance->controller;
    }


    /**
     * run action
     */
    private function startAction()
    {
        //$actionName = strtolower(substr($this->actionName, 6));
        $this->controller = new $this->controllerName($this->actionSlug, $this->actionParams);
        if ($this->controller) {
            $method = $this->actionName;
            echo $this->controller->$method();
            exit;
        } else {
            if (App::getComponent('config')->get('debug')) {
                echo 'Error! Controller or action not found';
            }
            exit;
        }
    }

    /**
     * make links to empty components
     */
    private function makeComponents()
    {
        $componentsDirectory = $this->rootPath . '/app/components';
        $componentFiles = array_diff(scandir($componentsDirectory), ['.', '..']);
        foreach ($componentFiles as $file) {
            if (is_file($componentsDirectory . '/' . $file)) {
                $fileKey = str_replace('.php', '', $file);
                if (class_exists('app\components\\' . $fileKey) &&
                    is_subclass_of('app\components\\' . $fileKey, 'app\Component')) {
                    self::$instance->components[$fileKey] = null;
                }
            }
        }
    }

    /**
     * if wrong request
     */
    private function errorNotFound()
    {
        $this->controllerName = 'app\Controller';
        $this->actionName = 'actionNotfound';
        $this->controller = new Controller();
        $this->controller->actionNotfound();
    }
    /**
     * if access denyed
     */
    private function error405()
    {
        $this->controllerName = 'app\Controller';
        $this->actionName = 'actionNotaccess';
        $this->controller = new Controller('notaccess');
        $this->controller->actionNotaccess();
    }

    /**
     * @param $url
     */
    private function redirect($url)
    {
        $this->controllerName = 'app\Controller';
        $this->controller = new Controller();
        $this->controller->redirect($url);
    }

}