<?php
/**
 * This file is part of the Everon components.
 *
 * (c) Oliwier Ptak <everonphp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory;

use Everon\Component\Collection\Collection;
use Everon\Component\Collection\CollectionInterface;
use Everon\Component\Factory\Dependency\ContainerInterface;
use Everon\Component\Factory\Dependency\FactoryAwareInterface;
use Everon\Component\Factory\Exception\InstanceIsAbstractClassException;
use Everon\Component\Factory\Exception\MissingFactoryAwareInterfaceException;
use Everon\Component\Factory\Exception\UnableToInstantiateException;
use Everon\Component\Factory\Exception\UndefinedClassException;

class Factory implements FactoryInterface
{

    /**
     * @var ContainerInterface
     */
    protected static $DependencyContainer;

    /**
     * @var FactoryWorkerInterface[]|CollectionInterface
     */
    protected static $WorkerCollection;

    /**
     * @param ContainerInterface $Container
     */
    public function __construct(ContainerInterface $Container)
    {
        static::$DependencyContainer = $Container;
        static::$WorkerCollection = new Collection([]);
    }

    /**
     * @inheritdoc
     */
    public function injectDependencies($className, $Instance)
    {
        $this->getDependencyContainer()->inject($className, $Instance);
        $this->injectFactoryWhenRequired($className, $Instance);
    }

    /**
     * @inheritdoc
     */
    public function injectDependenciesOnce($className, $Instance)
    {
        $this->getDependencyContainer()->injectOnce($className, $Instance);
        $this->injectFactoryWhenRequired($className, $Instance);
    }

    /**
     * @param $className
     * @param object $Instance
     *
     * @throws MissingFactoryAwareInterfaceException
     *
     * @return void
     */
    protected function injectFactoryWhenRequired($className, $Instance)
    {
        if ($this->getDependencyContainer()->isFactoryRequired($className)) {
            if (($Instance instanceof FactoryAwareInterface) === false) {
                throw new MissingFactoryAwareInterfaceException($className);
            }
            /* @var FactoryAwareInterface $Instance */
            $Instance->setFactory($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildWithEmptyConstructor($className, $namespace)
    {
        $className = $this->getFullClassName($namespace, $className);
        $this->classExists($className);

        $Instance = new $className();

        $this->injectDependencies($className, $Instance);

        return $Instance;
    }

    /**
     * @inheritdoc
     */
    public function buildWithConstructorParameters($className, $namespace, CollectionInterface $parameterCollection)
    {
        $className = $this->getFullClassName($namespace, $className);
        $this->classExists($className);

        $ReflectionClass = new \ReflectionClass($className);

        if ($ReflectionClass->isInstantiable() === false) {
            if ($ReflectionClass->isAbstract()) {
                throw new InstanceIsAbstractClassException($className);
            } else {
                throw new UnableToInstantiateException($className);
            }
        }

        $Instance = $ReflectionClass->newInstanceArgs(
            array_values($parameterCollection->toArray())
        );

        $this->injectDependencies($className, $Instance);

        return $Instance;
    }

    /**
     * @inheritdoc
     */
    public function getDependencyContainer()
    {
        return static::$DependencyContainer;
    }

    /**
     * @inheritdoc
     */
    public function setDependencyContainer(ContainerInterface $Container)
    {
        static::$DependencyContainer = $Container;
    }

    /**
     * @inheritdoc
     */
    public function getFullClassName($namespace, $className)
    {
        if ($className[0] === '\\') { //used for when laading classmap from cache
            return $className; //absolute name
        }

        return $namespace . '\\' . $className;
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
     * @inheritdoc
     */
    public function buildParameterCollection(array $parameters)
    {
        return new Collection($parameters);
    }

    /**
     * @inheritdoc
     */
    public function getWorkerByName($name, $namespace='Everon\Component\Factory')
    {
        $className = sprintf('%sFactoryWorker', $name);

        if (static::$WorkerCollection->has($className)) {
            return static::$WorkerCollection->get($className);
        }

        /** @var FactoryWorkerInterface $Worker */
        $Worker = $this->buildWithConstructorParameters($className, $namespace, $this->buildParameterCollection([
            $this,
        ]));

        $Worker->doWork();

        static::$WorkerCollection->set($className, $Worker);

        return static::$WorkerCollection->get($className);
    }

}
