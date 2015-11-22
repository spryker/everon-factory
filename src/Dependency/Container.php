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

use Everon\Component\Factory\Exception\DependencyCannotInjectItselfIntoItselfException;
use Everon\Component\Factory\Exception\InstanceIsNotObjectException;
use Everon\Component\Factory\Exception\UndefinedContainerDependencyException;
use Everon\Component\Factory\Exception\UndefinedClassException;
use Everon\Component\Factory\Exception\UndefinedDependencySetterException;
use Everon\Component\Utils\Text\EndsWith;
use Everon\Component\Utils\Text\LastTokenToName;

class Container implements ContainerInterface
{
    use EndsWith;
    use LastTokenToName;

    const DEPENDENCY_INJECTION_FACTORY = 'Dependency\Injection\Factory';

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
     * @param $setter_name
     * @param $Instance
     *
     * @throws UndefinedContainerDependencyException
     * @throws UndefinedDependencySetterException
     * @internal param $dependency_name
     */
    protected function injectSetterDependency($setter_name, $Instance)
    {
        $method = 'set'.$setter_name; //eg. setConfigManager
        if (method_exists($Instance, $method) === false) {
            throw new UndefinedDependencySetterException([
                get_class($Instance),
                $setter_name
            ]);
        }

        $Dependency = $this->resolve($setter_name);
        $Instance->$method($Dependency);
    }

    /**
     * @param $class_name
     * @param bool $autoload
     * @return array
     */
    protected function getClassDependencies($class_name, $autoload = true)
    {
        $traits = class_uses($class_name, $autoload);
        $parents = class_parents($class_name, $autoload);

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
    public function inject($ReceiverInstance)
    {
        if (is_object($ReceiverInstance) === false) {
            throw new InstanceIsNotObjectException();
        }

        $receiverClassName = get_class($ReceiverInstance);

        $dependencies = $this->getClassDependencies($receiverClassName);
        foreach ($dependencies as $dependencyName) {
            if ($this->textEndsWith($dependencyName, static::DEPENDENCY_INJECTION_FACTORY)) {
                $this->requiresFactory[$receiverClassName] = true;
                continue;
            }

            $requiredDependency = $this->textLastTokenToName($dependencyName);

            if (strcasecmp($requiredDependency, $this->textLastTokenToName($receiverClassName)) === 0) {
                throw new DependencyCannotInjectItselfIntoItselfException($receiverClassName);
            }

            $this->injectSetterDependency($requiredDependency, $ReceiverInstance);
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
