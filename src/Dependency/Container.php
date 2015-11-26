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

use Everon\Component\Factory\Exception\DependencyCannotInjectItselfException;
use Everon\Component\Factory\Exception\DependencyServiceAlreadyRegisteredException;
use Everon\Component\Factory\Exception\InstanceIsNotObjectException;
use Everon\Component\Factory\Exception\UndefinedContainerDependencyException;
use Everon\Component\Factory\Exception\UndefinedDependencySetterException;
use Everon\Component\Utils\Text\EndsWith;
use Everon\Component\Utils\Text\LastTokenToName;

class Container implements ContainerInterface
{
    use EndsWith;
    use LastTokenToName;

    const DEPENDENCY_INJECTION_FACTORY = 'Dependency\Factory';
    const TYPE_SETTER_INJECTION = 'Dependency\Setter';

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
    protected $dependencies = [];

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
                $method,
                $setter_name,
                get_class($Instance)
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

        return array_keys($traits);
    }

    /**
     * @inheritdoc
     */
    public function inject($receiver_class_name, $ReceiverInstance)
    {
        if (is_object($ReceiverInstance) === false) {
            throw new InstanceIsNotObjectException();
        }

        $dependencies = $this->getClassDependencies($receiver_class_name);
        foreach ($dependencies as $dependencyName) {
            if ($this->textEndsWith($dependencyName, static::DEPENDENCY_INJECTION_FACTORY)) {
                $this->requiresFactory[$receiver_class_name] = true;
                continue;
            }

            $requiredDependency = $this->textLastTokenToName($dependencyName);

            if (strcasecmp($requiredDependency, $this->textLastTokenToName($receiver_class_name)) === 0) {
                throw new DependencyCannotInjectItselfException($receiver_class_name);
            }

            $setterDependency = sprintf('%s\%s', static::TYPE_SETTER_INJECTION, $requiredDependency);
            $isSetterInjection = $this->textEndsWith(
                $dependencyName,
                $setterDependency
            );

            if ($isSetterInjection) {
                $this->injectSetterDependency($requiredDependency, $ReceiverInstance);
            }
        }

        $this->dependencies[$receiver_class_name] = $dependencies;
    }

    /**
     * @inheritdoc
     */
    public function injectOnce($receiver_class_name, $ReceiverInstance)
    {
        if ($this->isInjected($receiver_class_name)) {
            return;
        }

        $this->inject($receiver_class_name, $ReceiverInstance);
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
        if ($this->isRegistered($name)) {
            throw new DependencyServiceAlreadyRegisteredException($name);
        }

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
    public function isInjected($name)
    {
        return isset($this->dependencies[$name]);
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
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * @inheritdoc
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @inheritdoc
     */
    public function getServices()
    {
        return $this->services;
    }

}
