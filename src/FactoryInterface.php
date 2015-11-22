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


use Everon\Component\Factory\Dependency\ContainerInterface;
use Everon\Component\Factory\Exception\UndefinedClassException;

interface FactoryInterface
{

    /**
     * @param $class_name
     * @param $Instance
     * @return
     */
    public function injectDependencies($class_name, $Instance);

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
     * @param $class_name
     *
     * @return string
     */
    public function getFullClassName($namespace, $class_name);

    /**
     * @param $class
     *
     * @throws UndefinedClassException
     * @return void
     */
    public function classExists($class);

}
