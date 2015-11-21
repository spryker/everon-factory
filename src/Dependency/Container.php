<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <EveronFramework@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Dependency;

use Everon\Component\Factory\Exception\UndefinedContainerDependencyException;
use Everon\Component\Factory\Exception\UndefinedClassException;
use Everon\Component\Factory\Exception\UndefinedDependencySetterException;

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    protected $definitions = [];

    /**
     * @var array
     */
    protected $services = [];

    /**
     * @var array
     */
    protected $requiresFactory = [];


    /**
     * @param $dependency_name
     * @param $setter_name
     * @param $Receiver
     *
     * @throws UndefinedContainerDependencyException
     * @throws UndefinedDependencySetterException
     * @return void
     */
    protected function injectSetterDependency($dependency_name, $setter_name, $Receiver)
    {
        $method = 'set'.$setter_name; //eg. setConfigManager
        if (method_exists($Receiver, $method) === false) {
            throw new UndefinedDependencySetterException([
                $dependency_name,
                $setter_name
            ]);
        }

        $Receiver->$method($this->resolve($setter_name));
    }

    /**
     * @param $class
     * @param bool $autoload
     * @return array
     */
    protected function getClassDependencies($class, $autoload = true)
    {
        $traits = class_uses($class, $autoload);
        $parents = class_parents($class, $autoload);

        foreach ($parents as $parent) {
            $traits = array_merge(
                class_uses($parent, $autoload), 
                $traits
            );
        }

        return $traits;
    }

    /**
     * @inheritdoc
     */
    public function inject($class_name, $Receiver)
    {
        if (class_exists($class_name, true) === false) {
            throw new UndefinedClassException($class_name);
        }

    }

    /**
     * @inheritdoc
     */
    public function isFactoryRequired($class_name)
    {
        return isset($this->requiresFactory[$class_name]) && $this->requiresFactory[$class_name];
    }

    /**
     * @inheritdoc
     */
    public function register($name, \Closure $ServiceClosure)
    {
        $this->definitions[$name] = $ServiceClosure;
        unset($this->services[$name]);
    }

    /**
     * @inheritdoc
     */
    public function propose($name, \Closure $ServiceClosure)
    {
        if ($this->isRegistered($name)) {
            return;
        }
        
        $this->register($name, $ServiceClosure);
    }

    /**
     * @inheritdoc
     */
    public function resolve($name)
    {
        if (isset($this->definitions[$name]) === false) {
            throw new UndefinedContainerDependencyException($name);
        }

        if (isset($this->services[$name])) {
            return $this->services[$name];
        }
        
        if (is_callable($this->definitions[$name])) {
            $this->services[$name] = $this->definitions[$name]();
        }
        
        return $this->services[$name];
    }

    /**
     * @inheritdoc
     */
    public function isRegistered($name)
    {
        return (isset($this->definitions[$name]) || isset($this->services[$name]));
    }

    /**
     * @inheritdoc
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @inheritdoc
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

}
