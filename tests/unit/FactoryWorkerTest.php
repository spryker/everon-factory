<?php
/**
 * This file is part of the Everon components.
 *
 * (c) Oliwier Ptak <everonphp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Tests\Unit;

use Everon\Component\Collection\CollectionInterface;
use Everon\Component\Factory\FactoryInterface;
use Everon\Component\Factory\Tests\Unit\Doubles\StubFactoryWorker;
use Everon\Component\Utils\TestCase\MockeryTest;
use Mockery;
use Mockery\MockInterface;

class FactoryWorkerTest extends MockeryTest
{

    /**
     * @var StubFactoryWorker
     */
    protected $FactoryWorker;

    protected function setUp()
    {
        $Factory = Mockery::mock('Everon\Component\Factory\FactoryInterface');

        /* @var FactoryInterface $Factory */
        $this->FactoryWorker = new StubFactoryWorker($Factory);
    }

    public function test_do_work()
    {
        $Container = Mockery::mock('Everon\Component\Factory\Dependency\ContainerInterface');

        $Factory = $this->FactoryWorker->getFactory();
        /* @var MockInterface $Factory */
        $Factory->shouldReceive('registerWorkerCallback')->times(1)
            ->andReturn($Container);

        $this->FactoryWorker->doWork();
    }

}
