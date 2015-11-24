<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <EveronFramework@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Tests\Unit\Doubles\Dependency;

use Everon\Component\Factory\Tests\Unit\Doubles\GizzStub;

trait Gizz
{
    /**
     * @var GizzStub
     */
    protected $Gizz;

    /**
     * @return GizzStub
     */
    public function getGizz()
    {
        return $this->Gizz;
    }

    /**
     * @param GizzStub $Gizz
     */
    public function setGizz(GizzStub $Gizz)
    {
        $this->Gizz = $Gizz;
    }

}
