<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <EveronFramework@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory;

use Everon\Component\Collection\Collection;
use Everon\Component\Collection\CollectionInterface;
use Everon\Component\Factory\Dependency\ContainerInterface;
use Everon\Component\Factory\Dependency\FactoryDependencyInterface;
use Everon\Component\Factory\Exception\InstanceIsAbstractClassException;
use Everon\Component\Factory\Exception\MissingFactoryDependencyInterfaceException;
use Everon\Component\Factory\Exception\UnableToInstantiateException;
use Everon\Component\Factory\Exception\UndefinedClassException;

class Factory implements FactoryInterface
{

    /**
     * @var ContainerInterface
     */
    protected $DependencyContainer;

    /**
     * @var FactoryWorkerInterface[]
     */
    protected $WorkerCollection;


    /**
     * @param ContainerInterface $Container
     */
    public function __construct(ContainerInterface $Container)
    {
        $this->DependencyContainer = $Container;
        $this->WorkerCollection = new Collection([]);
    }

    /**
     * @inheritdoc
     */
    public function injectDependencies($class_name, $Instance)
    {
        $this->getDependencyContainer()->inject($class_name, $Instance);
        if ($this->getDependencyContainer()->isFactoryRequired($class_name)) {
            if (($Instance instanceof FactoryDependencyInterface) === false) {
                throw new MissingFactoryDependencyInterfaceException($class_name);
            }
            /** @var FactoryDependencyInterface $Instance */
            $Instance->setFactory($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function injectDependenciesOnce($class_name, $Instance)
    {
        $this->getDependencyContainer()->injectOnce($class_name, $Instance);
        if ($this->getDependencyContainer()->isFactoryRequired($class_name)) {
            if (($Instance instanceof FactoryDependencyInterface) === false) {
                throw new MissingFactoryDependencyInterfaceException($class_name);
            }
            /** @var FactoryDependencyInterface $Instance */
            $Instance->setFactory($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildWithEmptyConstructor($class_name, $namespace)
    {
        $class_name = $this->getFullClassName($namespace, $class_name);
        $this->classExists($class_name);

        $Instance = new $class_name();

        $this->injectDependencies($class_name, $Instance);

        return $Instance;
    }

    /**
     * @inheritdoc
     */
    public function buildWithConstructorParameters($class_name, $namespace, CollectionInterface $parameterCollection)
    {
        $class_name = $this->getFullClassName($namespace, $class_name);
        $this->classExists($class_name);

        $ReflectionClass = new \ReflectionClass($class_name);

        if ($ReflectionClass->isInstantiable() === false) {
            if ($ReflectionClass->isAbstract()) {
                throw new InstanceIsAbstractClassException($class_name);
            } else {
                throw new UnableToInstantiateException($class_name);
            }
        }

        $Instance = $ReflectionClass->newInstanceArgs(
            array_values($parameterCollection->toArray())
        );

        $this->injectDependencies($class_name, $Instance);

        return $Instance;
    }

    /**
     * @inheritdoc
     */
    public function getDependencyContainer()
    {
        return $this->DependencyContainer;
    }

    /**
     * @inheritdoc
     */
    public function setDependencyContainer(ContainerInterface $Container)
    {
        $this->DependencyContainer = $Container;
    }

    /**
     * @inheritdoc
     */
    public function getFullClassName($namespace, $class_name)
    {
        if ($class_name[0] === '\\') { //used for when laading classmap from cache
            return $class_name; //absolute name
        }

        return $namespace . '\\' . $class_name;
    }

    /**
     * @inheritdoc
     */
    public function classExists($class)
    {
        if (class_exists($class, true) === false) {
            throw new UndefinedClassException($class);
        }
    }

    /**
     * @param array $parameters
     *
     * @return CollectionInterface
     */
    public function buildParameterCollection(array $parameters)
    {
        return new Collection($parameters);
    }

    /**
     * @param $name
     *
     * @return FactoryWorkerInterface
     */
    public function getWorkerByName($name, $namespace='Everon\Component\Factory')
    {
        $className = sprintf('%sFactoryWorker', $name);

        if ($this->WorkerCollection->has($className)) {
            return $this->WorkerCollection->get($className);
        }

        $Worker = $this->buildWithConstructorParameters($className, $namespace, $this->buildParameterCollection([
            $this
        ]));

        $this->WorkerCollection->set($name, $Worker);

        return $this->WorkerCollection->get($name);
    }

}
