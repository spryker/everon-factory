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
use Everon\Component\Factory\Dependency\Container;
use Everon\Component\Factory\Dependency\ContainerInterface;
use Everon\Component\Factory\Tests\Unit\Doubles\FactoryStub;
use Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub;
use Mockery;

class DependencyContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $Container;

    /**
     * @var FactoryStub
     */
    protected $Factory;


    protected function setUp()
    {
        $this->Container = new Container();
        $this->Factory = new FactoryStub($this->Container);
        $Factory = $this->Factory;

        $this->Container->register('Foo', function() use ($Factory) {
            //requires setter injection of Bar, see FooStub
            return $Factory->buildFoo();
        });

        $this->Container->register('Bar', function() use ($Factory) {
            //requires no dependencies, see GizzStub
            $Gizz = $Factory->buildGizz();

            //requires constructor injection of $Gizz, see BarStub
            return $Factory->buildBar($Gizz, 'argument', [
                'some' => 'data'
            ]);
        });

        $this->Container->register('Gizz', function() use ($Factory) {
            return $Factory->buildGizz();
        });
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_setter_dependency_injection()
    {
        $Fuzz = new FuzzStub();

        /** @var CollectionInterface $ParameterCollection  */
        $this->Container->inject($Fuzz);

        $this->assertInstanceOf('Everon\Component\Factory\Tests\Unit\Doubles\FooStub', $Fuzz->getFoo());
        $this->assertInstanceOf('Everon\Component\Factory\Tests\Unit\Doubles\BarStub', $Fuzz->getFoo()->getBar());
    }

    public function test_setter_dependency_should_only_be_injected_once()
    {
        $Fuzz = new FuzzStub();

        /** @var CollectionInterface $ParameterCollection  */
        $this->Container->inject($Fuzz);
        $this->Container->inject($Fuzz);

        $this->assertTrue($this->Container->isInjected(get_class($Fuzz)));
    }

}
