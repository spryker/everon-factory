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

trait FactoryFactoryWorker
{

    /**
     * @var FactoryFactoryWorkerInterface
     */
    protected $FactoryFactoryWorker;


    /**
     * @return FactoryFactoryWorkerInterface
     */
    public function getFactoryFactoryWorker()
    {
        return $this->FactoryFactoryWorker;
    }

    /**
     * @param FactoryFactoryWorkerInterface $FactoryFactoryWorker
     */
    public function setFactoryFactoryWorker(FactoryFactoryWorkerInterface $FactoryFactoryWorker)
    {
        $this->FactoryFactoryWorker = $FactoryFactoryWorker;
    }

}
