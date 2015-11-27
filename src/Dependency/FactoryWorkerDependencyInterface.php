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

use Everon\Component\Factory\FactoryFactoryWorkerInterface;

interface FactoryWorkerDependencyInterface
{

    /**
     * @return FactoryFactoryWorkerInterface
     */
    public function getFactoryFactoryWorker();

    /**
     * @param FactoryFactoryWorkerInterface $FactoryFactoryWorker
     */
    public function setFactoryFactoryWorker(FactoryFactoryWorkerInterface $FactoryFactoryWorker);

}
