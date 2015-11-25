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
use Everon\Component\Factory\Dependency\ContainerInterface;
use Everon\Component\Factory\Factory;
use Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub;
use Mockery;
use Mockery\MockInterface;
use Everon\Component\Factory\Tests\Unit\Doubles\FactoryStub;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FactoryStub
     */
    protected $Factory;


    protected function setUp()
    {
        $Container = Mockery::mock('Everon\Component\Factory\Dependency\ContainerInterface');

        /** @var ContainerInterface $Container */
        $this->Factory = new Factory($Container);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_inject_dependencies_and_require_factory()
    {
        $Fuzz = Mockery::mock(
            'Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub, Everon\Component\Factory\Dependency\FactoryDependencyInterface'
        );

        $Fuzz->shouldReceive('setFactory')->times(1)->with($this->Factory);

        /** @var MockInterface $Container */
        $Container = $this->Factory->getDependencyContainer();
        $Container->shouldReceive('inject')->times(1)
            ->with($Fuzz);

        $Container->shouldReceive('isFactoryRequired')
            ->times(1)
            ->with('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub')
            ->andReturn(true);

        $this->Factory->injectDependencies('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub', $Fuzz);
    }

    public function test_inject_dependencies_without_factory()
    {
        $Fuzz = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub');

        /** @var MockInterface $Container */
        $Container = $this->Factory->getDependencyContainer();
        $Container->shouldReceive('inject')->times(1)->with($Fuzz);
        $Container->shouldReceive('isFactoryRequired')
            ->times(1)
            ->with('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub')
            ->andReturn(false);

        $this->Factory->injectDependencies('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub', $Fuzz);
    }

    public function test_build_with_empty_constructor()
    {
        /** @var MockInterface $Container */
        $Container = $this->Factory->getDependencyContainer();
        $Container->shouldReceive('inject')->times(1);
        $Container->shouldReceive('isFactoryRequired')
            ->times(1)
            ->with('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub')
            ->andReturn(false);

        $FuzzStub = $this->Factory->buildWithEmptyConstructor('FuzzStub', 'Everon\Component\Factory\Tests\Unit\Doubles');

        $this->assertInstanceOf('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub', $FuzzStub);
    }

    public function test_build_with_constructor_parameters()
    {
        $GizzStub = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\GizzStub');

        $CollectionParameters = Mockery::mock('Everon\Component\Collection\CollectionInterface');
        $CollectionParameters->shouldReceive('toArray')->times(1)->andReturn([
            $GizzStub,
            'argument', [
                'some' => 'data'
            ]
        ]);

        /** @var MockInterface $Container */
        $Container = $this->Factory->getDependencyContainer();
        $Container->shouldReceive('inject')->times(1);
        $Container->shouldReceive('isFactoryRequired')
            ->times(1)
            ->with('Everon\Component\Factory\Tests\Unit\Doubles\BarStub')
            ->andReturn(false);

        /** @var CollectionInterface $CollectionParameters */
        $BarStub = $this->Factory->buildWithConstructorParameters('BarStub', 'Everon\Component\Factory\Tests\Unit\Doubles', $CollectionParameters);

        $this->assertInstanceOf('Everon\Component\Factory\Tests\Unit\Doubles\BarStub', $BarStub);
    }
}
