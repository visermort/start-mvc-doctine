<?php

namespace app\lib\console;

use app\App;

/**
 * Class ConsoleRunner
 * @package app\lib\console
 */
class ConsoleRunner
{
    private $className;
    private $actionName;
    private $controllersNameSpace = 'app\console\controllers';
    private $parentClassName = 'app\console\Controller';
    private $controllersPath = '/app/console/controllers';
    private $arguments;

    /**
     * ConsoleHandler constructor.
     * @param $arguments
     */
    public function __construct($arguments)
    {
        $this->arguments = $arguments;
        $actionPath = isset($arguments[1]) ? explode('/', $arguments[1]) : false;
        if (isset($actionPath[1])) {
            $this->className = $this->controllersNameSpace . '\\' . ucfirst($actionPath[0]) . 'Controller';
            $this->actionName = 'action' . App::getComponent('help')->commandToAction($actionPath[1]);
        }
    }

    /**
     * @return bool
     */
    public function actionExists()
    {
        return $this->className && $this->actionName &&
            method_exists($this->className, $this->actionName) &&
            is_subclass_of($this->className, $this->parentClassName);
    }

    public function actionRun()
    {
        $params = array_slice($this->arguments, 2);
        $instance = new $this->className();
        $actionName = $this->actionName;
        $instance->$actionName(...$params);
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function getEnabledCommands()
    {
        $out = '';
        $controllersFullPath = App::getRequest('root_path') . $this->controllersPath;
        $controllerFiles = array_diff(scandir($controllersFullPath), ['.', '..']);
        foreach ($controllerFiles as $file) {
            if (is_dir($controllersFullPath . '/' . $file)) {
                continue;
            }
            $file = substr($file, 0, -4);
            $className = $this->controllersNameSpace . '\\' . ucfirst($file);
            if (!class_exists($className) || !is_subclass_of($className, $this->parentClassName)) {
                continue;
            }
            $reflectionClass = new \ReflectionClass($className);
            $controllerIndex = strtolower(substr($file, 0, -10));
            $controllerComment = trim(preg_replace(
                "/[\/\*\r\n]/",
                '',
                $reflectionClass->getDocComment()
            ));

            $methods = get_class_methods($className);
            if (!empty($methods)) {
                $out .=  $controllerIndex."\n";
                $out .= $controllerComment."\n";
                foreach ($methods as $method) {
                    if (substr($method, 0, 6) == 'action') {
                        $methodIndex = App::getComponent('help')->commandToAction(substr($method, 6), true);
                        $reflection = new \ReflectionMethod($className, $method);

                        $comment = trim(preg_replace(
                            "/[\/\*\r\n]/",
                            '',
                            $reflection->getDocComment()
                        ));
                        $out .= $controllerIndex . '/' . $methodIndex . '    ' . $comment . "\n";
                    }
                }
            }
        }
        return $out;
    }


}
