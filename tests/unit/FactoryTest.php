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

use Everon\Component\Factory\Dependency\Container;
use Everon\Component\Factory\Dependency\ContainerInterface;
use Everon\Component\Factory\Tests\Unit\Doubles\FactoryStub;

class FactoryTest extends \PHPUnit_Framework_TestCase
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

        $this->Container->register('Foo', function () use ($Factory) {
            return $Factory->buildFoo();
        });

        $this->Container->register('Bar', function () use ($Factory) {
            return $Factory->buildBar();
        });
    }

    public function test_build()
    {
        $Fuzz = $this->Factory->buildFuzz();

        $this->assertInstanceOf('Everon\Component\Factory\Tests\Unit\Doubles\FooStub', $Fuzz->getFoo());
        $this->assertInstanceOf('Everon\Component\Factory\Tests\Unit\Doubles\BarStub', $Fuzz->getFoo()->getBar());
    }

}
