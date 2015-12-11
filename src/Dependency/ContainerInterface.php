<?php
/**
 * This file is part of the Everon components.
 *
 * (c) Oliwier Ptak <everonphp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Dependency;

use Everon\Component\Collection\CollectionInterface;
use Everon\Component\Factory\Exception\DependencyServiceAlreadyRegisteredException;
use Everon\Component\Factory\Exception\UndefinedContainerDependencyException;

interface ContainerInterface
{

    /**
     * @param string $receiver_class_name
     * @param object $ReceiverInstance
     *
     * @return void
     */
    public function inject($receiver_class_name, $ReceiverInstance);

    /**
     * @param string $receiver_class_name
     * @param object $ReceiverInstance
     *
     * @return void
     */
    public function injectOnce($receiver_class_name, $ReceiverInstance);

    /**
     * @param $class_name
     *
     * @return bool
     */
    public function isFactoryRequired($class_name);

    /**
     * @param $name
     * @param \Closure $ServiceClosure
     *
     * @throws DependencyServiceAlreadyRegisteredException
     *
     * @return void
     */
    public function register($name, \Closure $ServiceClosure);

    /**
     * @param $name
     * @param \Closure $ServiceClosure
     *
     * @return void
     */
    public function propose($name, \Closure $ServiceClosure);

    /**
     * @param $name
     *
     * @throws UndefinedContainerDependencyException
     *
     * @return mixed
     */
    public function resolve($name);

    /**
     * @param $class_name
     *
     * @return bool
     */
    public function isInjected($class_name);

    /**
     * @param $name
     *
     * @return bool
     */
    public function isRegistered($name);

    /**
     * @return CollectionInterface
     */
    public function getServiceDefinitionCollection();

    /**
     * @return CollectionInterface
     */
    public function getClassDependencyCollection();

    /**
     * @return CollectionInterface
     */
    public function getServiceCollection();

    /**
     * @return CollectionInterface
     */
    public function getRequireFactoryCollection();

    /**
     * @return CollectionInterface
     */
    public function getInjectedCollection();

}
