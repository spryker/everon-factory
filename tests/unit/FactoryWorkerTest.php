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
use Mockery;
use Mockery\MockInterface;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class FactoryWorkerTest extends \PHPUnit_Framework_TestCase
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
        $GizzMock = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\GizzStub');

        $Factory = $this->FactoryWorker->getFactory();
        /* @var MockInterface $Factory */
        $Factory->shouldReceive('buildWithEmptyConstructor')->times(1)
            ->with('GizzStub', 'Everon\Component\Factory\Tests\Unit\Doubles')
            ->andReturn($GizzMock);

        $GizzStub = $this->FactoryWorker->buildGizz();

        $this->assertInstanceOf(get_class($GizzStub), $GizzMock);
    }

    public function test_build_with_constructor_parameters()
    {
        $BarStubMock = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\BarzStub');
        $GizzStub = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\GizzStub');
        $CollectionParameters = Mockery::mock('Everon\Component\Collection\CollectionInterface');

        $parameters = [
            $GizzStub,
            'anotherArgument', [
                'some' => 'data',
            ],
        ];

        $Factory = $this->FactoryWorker->getFactory();
        /* @var MockInterface $Factory */
        $Factory->shouldReceive('buildParameterCollection')->times(1)
            ->with($parameters)
            ->andReturn($CollectionParameters);

        $Factory->shouldReceive('buildWithEmptyConstructor')->times(1)
            ->with('GizzStub', 'Everon\Component\Factory\Tests\Unit\Doubles')
            ->andReturn($GizzStub);

        $Factory->shouldReceive('buildWithConstructorParameters')->times(1)
            ->with('BarStub', 'Everon\Component\Factory\Tests\Unit\Doubles', $CollectionParameters)
            ->andReturn($BarStubMock);

        /* @var CollectionInterface $CollectionParameters */
        $BarStub = $this->FactoryWorker->buildBar('anotherArgument', [
            'some' => 'data',
        ], 'Everon\Component\Factory\Tests\Unit\Doubles');

        $this->assertInstanceOf(get_class($BarStub), $BarStubMock);
    }
}
