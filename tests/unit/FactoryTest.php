<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <EveronFramework@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Utils\Tests\Unit;

use Everon\Component\Factory\Dependency\Container;
use Everon\Component\Utils\Tests\Unit\Doubles\FactoryStub;

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

    public function test_dependency_injection()
    {
        $Foo = $this->Factory->buildFoo();

        sd($Foo);

        $this->assertInstanceOf('Everon\Component\Utils\Tests\Unit\Doubles\Foo', $Foo);
    }

}
