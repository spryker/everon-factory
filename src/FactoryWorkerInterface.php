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

use Everon\Component\Factory\Dependency\FactoryAwareInterface;

interface FactoryWorkerInterface extends FactoryAwareInterface
{

    /**
     * @return void
     */
    public function doWork();

}
