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

use Everon\Component\Collection\CollectionInterface;
use Everon\Component\Factory\Dependency\ContainerInterface;
use Everon\Component\Factory\Exception\InstanceIsAbstractClassException;
use Everon\Component\Factory\Exception\MissingFactoryAwareInterfaceException;
use Everon\Component\Factory\Exception\UnableToInstantiateException;
use Everon\Component\Factory\Exception\UndefinedClassException;

interface FactoryInterface
{

    /**
     * @param $className
     * @param $Instance
     *
     * @return void
     */
    public function injectDependencies($className, $Instance);

    /**
     * @param $className
     * @param $Instance
     *
     * @return void
     */
    public function injectDependenciesOnce($className, $Instance);

    /**
     * @param $className
     * @param $namespace
     *
     * @throws MissingFactoryAwareInterfaceException
     * @throws UndefinedClassException
     *
     * @return object
     */
    public function buildWithEmptyConstructor($className, $namespace);

    /**
     * @param $className
     * @param $namespace
     * @param CollectionInterface $parameterCollection
     *
     * @throws MissingFactoryAwareInterfaceException
     * @throws UndefinedClassException
     *
     * @return object
     */
    public function buildWithConstructorParameters($className, $namespace, CollectionInterface $parameterCollection);

    /**
     * @return ContainerInterface
     */
    public function getDependencyContainer();

    /**
     * @param ContainerInterface $Container
     *
     * @return void
     */
    public function setDependencyContainer(ContainerInterface $Container);

    /**
     * @param $namespace
     * @param $className
     *
     * @return string
     */
    public function getFullClassName($namespace, $className);

    /**
     * @param $class
     *
     * @throws UndefinedClassException
     *
     * @return void
     */
    public function classExists($class);

    /**
     * @param array $parameters
     *
     * @return CollectionInterface
     */
    public function buildParameterCollection(array $parameters);

    /**
     * @param $name
     * @param string $namespace
     *
     * @throws InstanceIsAbstractClassException
     * @throws UnableToInstantiateException
     *
     * @return FactoryWorkerInterface
     */
    public function getWorkerByName($name, $namespace='Everon\Component\Factory');

}
