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

class FactoryFactoryWorker extends AbstractWorker implements FactoryFactoryWorkerInterface
{

    /**
     * @inheritdoc
     */
    protected function registerBeforeWork()
    {
        $Factory = $this->getFactory();
        $this->getFactory()->getDependencyContainer()->propose('FactoryFactoryWorker', function () use ($Factory) {
            return $Factory->getWorkerByName('Factory', 'Everon\Component\Factory');
        });
    }

}
