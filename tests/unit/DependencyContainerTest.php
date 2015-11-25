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
use Everon\Component\Factory\Exception\DependencyServiceAlreadyRegisteredException;
use Everon\Component\Factory\Tests\Unit\Doubles\FactoryStub;
use Everon\Component\Factory\Tests\Unit\Doubles\FooStub;
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

        //everything used with resolve() must be registered with register

        $this->Container->register('Logger', function () use ($Factory) {
            return $Factory->buildLogger();
        });

        $this->Container->register('Fuzz', function () use ($Factory) {
            //requires constructor injection of Foo, see FooStub
            //creates always new instance of Foo
            $FooStub = $Factory->buildFoo();
            return $Factory->buildFuzz($FooStub);
        });

        $this->Container->register('Foo', function () use ($Factory) {
            //requires setter injection of Bar, see FooStub
            //Bar requires constructor injection of Logger
            return $Factory->buildFoo();
        });

        $this->Container->register('Bar', function () use ($Factory) {
            //requires constructor injection of $Logger, see BarStub
            //uses same Logger instance
            $Logger = $Factory->getDependencyContainer()->resolve('Logger');
            return $Factory->buildBar($Logger, 'argument', [
                'some' => 'data'
            ]);
        });
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_setter_dependency_injection_one_logger_instance()
    {
        $Foo = new FooStub();

        $this->Container->inject($Foo);

        $this->assertInstanceOf('Everon\Component\Factory\Tests\Unit\Doubles\BarStub', $Foo->getBar());
        $this->assertInstanceOf('Everon\Component\Factory\Tests\Unit\Doubles\LoggerStub', $Foo->getBar()->getLogger());

        $this->assertEquals($Foo->getLogger(), $Foo->getBar()->getLogger());
    }

    public function test_setter_dependency_should_only_be_injected_once()
    {
        $FooStub = new FooStub();
        $Fuzz = new FuzzStub($FooStub);
        $ExpectedFuzz = clone $Fuzz;

        $this->Container->inject($Fuzz);
        $this->assertEquals($ExpectedFuzz, $Fuzz);

        $this->Container->inject($Fuzz);
        $this->assertEquals($ExpectedFuzz, $Fuzz);

        $this->assertTrue($this->Container->isInjected(get_class($Fuzz)));
    }

    /**
     * @expectedException \Everon\Component\Factory\Exception\DependencyServiceAlreadyRegisteredException
     * @expectedExceptionMessage Dependency service "Fuzz" is already registered
     */
    public function test_service_only_registers_once()
    {
        $Factory = $this->Factory;

        $this->Container->register('Fuzz', function () use ($Factory) {
            $FooStub = $Factory->buildFoo();
            return $Factory->buildFuzz($FooStub);
        });
    }

}
