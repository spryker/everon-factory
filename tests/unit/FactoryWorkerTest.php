<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <EveronFramework@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Tests\Unit;

use Everon\Component\Collection\CollectionInterface;
use Everon\Component\Factory\FactoryInterface;
use Everon\Component\Factory\Tests\Unit\Doubles\FactoryWorkerStub;
use Everon\Component\Utils\TestCase\MockeryTest;
use Mockery;
use Mockery\MockInterface;

class FactoryWorkerTest extends MockeryTest
{
    /**
     * @var FactoryWorkerStub
     */
    protected $FactoryWorker;

    protected function setUp()
    {
        $Factory = Mockery::mock('Everon\Component\Factory\FactoryInterface');

        /* @var FactoryInterface $Factory */
        $this->FactoryWorker = new FactoryWorkerStub($Factory);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_build_with_empty_constructor()
    {
        $LoggerMock = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\LoggerStub');

        $Factory = $this->FactoryWorker->getFactory();
        /* @var MockInterface $Factory */
        $Factory->shouldReceive('buildWithEmptyConstructor')->times(1)
            ->with('LoggerStub', 'Everon\Component\Factory\Tests\Unit\Doubles')
            ->andReturn($LoggerMock);

        $LoggerStub = $this->FactoryWorker->buildLogger();

        $this->assertInstanceOf(get_class($LoggerStub), $LoggerMock);
    }

    public function test_build_with_constructor_parameters()
    {
        $BarStubMock = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\BarzStub');
        $LoggerStub = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\LoggerStub');
        $CollectionParameters = Mockery::mock('Everon\Component\Collection\CollectionInterface');

        $parameters = [
            $LoggerStub,
            'anotherArgument', [
                'some' => 'data',
            ],
        ];

        $Container = Mockery::mock('Everon\Component\Factory\Dependency\ContainerInterface');
        $Container->shouldReceive('resolve')->times(1)
            ->with('Logger')
            ->andReturn($LoggerStub);

        $Factory = $this->FactoryWorker->getFactory();
        /* @var MockInterface $Factory */
        $Factory->shouldReceive('buildParameterCollection')->times(1)
            ->with($parameters)
            ->andReturn($CollectionParameters);

        $Factory->shouldReceive('buildWithEmptyConstructor')
            ->times(0) //this should never be called, we use same instance of Logger
            ->with('LoggerStub', 'Everon\Component\Factory\Tests\Unit\Doubles')
            ->andReturn($LoggerStub);

        $Factory->shouldReceive('buildWithConstructorParameters')->times(1)
            ->with('BarStub', 'Everon\Component\Factory\Tests\Unit\Doubles', $CollectionParameters)
            ->andReturn($BarStubMock);

        $Factory->shouldReceive('getDependencyContainer')->times(1)
            ->andReturn($Container);

        /* @var CollectionInterface $CollectionParameters */
        $BarStub = $this->FactoryWorker->buildBar('anotherArgument', [
            'some' => 'data',
        ], 'Everon\Component\Factory\Tests\Unit\Doubles');

        $this->assertInstanceOf(get_class($BarStub), $BarStubMock);
    }
}
