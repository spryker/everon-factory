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

use Everon\Component\Factory\Tests\Unit\Doubles\LoggerStub;

trait Logger
{
    /**
     * @var LoggerStub
     */
    protected $Logger;

    /**
     * @return LoggerStub
     */
    public function getLogger()
    {
        return $this->Logger;
    }

    /**
     * @param LoggerStub $Logger
     */
    public function setLogger(LoggerStub $Logger)
    {
        $this->Logger = $Logger;
    }

}
