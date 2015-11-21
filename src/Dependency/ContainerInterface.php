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

use Everon\Component\Factory\Exception\UndefinedClassException;

interface ContainerInterface
{
    /**
     * @param $class_name
     * @param $Receiver
     *
     * @throws UndefinedClassException
     * @return void
     */
    public function inject($class_name, $Receiver);

    /**
     * @param $class_name
     *
     * @return bool
     */
    public function isFactoryRequired($class_name);
}
