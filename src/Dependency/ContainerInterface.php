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

interface ContainerInterface
{
    /**
     * @param $ReceiverInstance
     *
     * @return
     */
    public function inject($ReceiverInstance);

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
     * @return mixed
     */
    public function resolve($name);

    /**
     * @param $name
     *
     * @return bool
     */
    public function isRegistered($name);

    /**
     * @return array
     */
    public function getServices();

    /**
     * @return array
     */
    public function getDefinitions();
}
