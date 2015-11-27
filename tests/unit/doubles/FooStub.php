<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <EveronFramework@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Tests\Unit\Doubles;

class FooStub
{

    use Dependency\Setter\Bar;

    protected $timeStamp = null;

    public function __construct()
    {
        $this->timeStamp = rand(0, time());
    }

    /**
     * @return LoggerStub
     */
    public function getLogger()
    {
        return $this->getBar()->getLogger();
    }

}
