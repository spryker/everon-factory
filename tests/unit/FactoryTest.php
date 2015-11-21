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
use Everon\Component\Factory\Tests\Unit\Doubles\FactoryStub;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FactoryStub
     */
    protected $Factory;


    protected function setUp()
    {
        $Dependency = new Container();
        $this->Factory = new FactoryStub($Dependency);
    }

    public function test_build()
    {
        $Fuzz = $this->Factory->buildFuzz();

        $this->assertInstanceOf('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub', $Fuzz);
    }

    public function test_dependency_injection()
    {
        $Foo = $this->Factory->buildFuzz();

        $Foo = $Foo->getFoo();

        $this->assertInstanceOf('Everon\Component\Factory\Tests\Unit\Doubles\FooStub', $Foo);
    }

}
