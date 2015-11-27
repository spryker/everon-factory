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

use Everon\Component\Collection\Collection;
use Everon\Component\Collection\CollectionInterface;
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
     * @var CollectionInterface
     */
    protected $ServiceDefinitionCollection;

    /**
     * @var CollectionInterface
     */
    protected $ServiceCollection;

    /**
     * @var CollectionInterface
     */
    protected $ClassDependencyCollection;

    /**
     * @var CollectionInterface
     */
    protected $RequireFactoryCollection;

    /**
     * @var CollectionInterface
     */
    protected $InjectedCollection;

    /**
     * @param $setter_name
     * @param $Receiver
     *
     * @throws UndefinedContainerDependencyException
     * @throws UndefinedDependencySetterException
     *
     * @internal param $dependency_name
     */
    protected function injectSetterDependency($setter_name, $Receiver)
    {
        $receiverClassName = get_class($Receiver);
        $method = 'set' . $setter_name; //eg. setConfigManager
        if (method_exists($Receiver, $method) === false) {
            throw new UndefinedDependencySetterException([
                $method,
                $setter_name,
                $receiverClassName,
            ]);
        }

        $Dependency = $this->resolve($setter_name);
        $Receiver->$method($Dependency);

        $this->getInjectedCollection()->set($receiverClassName, true);
    }

    /**
     * @param $class_name
     * @param bool $autoload
     *
     * @return array
     */
    protected function getClassDependencies($class_name, $autoload = true)
    {
        if ($this->getClassDependencyCollection()->has($class_name)) {
            return $this->getClassDependencyCollection()->get($class_name);
        }

        $traits = class_uses($class_name, $autoload);
        $parents = class_parents($class_name, $autoload);

        foreach ($parents as $parent) {
            $traits = array_merge(
                class_uses($parent, $autoload),
                $traits
            );
        }

        $dependencies = array_keys($traits);
        $this->getClassDependencyCollection()->set($class_name, $dependencies);

        return $this->getClassDependencyCollection()->get($class_name);
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
                $this->getRequireFactoryCollection()->set($receiver_class_name, true);
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
        return $this->getRequireFactoryCollection()->has($class_name) && $this->getRequireFactoryCollection()->get($class_name);
    }

    /**
     * @inheritdoc
     */
    public function register($name, \Closure $ServiceClosure)
    {
        if ($this->isRegistered($name)) {
            throw new DependencyServiceAlreadyRegisteredException($name);
        }

        $this->getServiceDefinitionCollection()->set($name, $ServiceClosure);

        $this->getServiceCollection()->remove($name);
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
        if ($this->getServiceDefinitionCollection()->has($name) === false) {
            throw new UndefinedContainerDependencyException($name);
        }

        if ($this->getServiceCollection()->has($name)) {
            return $this->getServiceCollection()->get($name);
        }

        /** @var \Closure $Service */
        $Service = $this->getServiceDefinitionCollection()->get($name);
        if (is_callable($Service)) {
            $this->getServiceCollection()->set($name, $Service());
        }

        return $this->getServiceCollection()->get($name);
    }

    /**
     * @inheritdoc
     */
    public function isInjected($class_name)
    {
        return $this->getInjectedCollection()->has($class_name) && $this->getInjectedCollection()->get($class_name);
    }

    /**
     * @inheritdoc
     */
    public function isRegistered($name)
    {
        return ($this->getServiceDefinitionCollection()->has($name) || $this->getServiceCollection()->has($name));
    }

    /**
     * @inheritdoc
     */
    public function getServiceDefinitionCollection()
    {
        if ($this->ServiceDefinitionCollection === null) {
            $this->ServiceDefinitionCollection = new Collection([]);
        }

        return $this->ServiceDefinitionCollection;
    }

    /**
     * @inheritdoc
     */
    public function getClassDependencyCollection()
    {
        if ($this->ClassDependencyCollection === null) {
            $this->ClassDependencyCollection = new Collection([]);
        }

        return $this->ClassDependencyCollection;
    }

    /**
     * @inheritdoc
     */
    public function getServiceCollection()
    {
        if ($this->ServiceCollection === null) {
            $this->ServiceCollection = new Collection([]);
        }

        return $this->ServiceCollection;
    }

    /**
     * @inheritdoc
     */
    public function getRequireFactoryCollection()
    {
        if ($this->RequireFactoryCollection === null) {
            $this->RequireFactoryCollection = new Collection([]);
        }

        return $this->RequireFactoryCollection;
    }

    /**
     * @inheritdoc
     */
    public function getInjectedCollection()
    {
        if ($this->InjectedCollection === null) {
            $this->InjectedCollection = new Collection([]);
        }

        return $this->InjectedCollection;
    }

}
