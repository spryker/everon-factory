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

use Everon\Component\Factory\Factory;

class FactoryStub extends Factory
{
    /**
     * @param string $namespace
     *
     * @return FuzzStub
     */
    public function buildFuzz(FooStub $FooStub, $namespace = 'Everon\Component\Factory\Tests\Unit\Doubles')
    {
        return $this->buildWithConstructorParameters('FuzzStub', $namespace, $this->buildParameterCollection([
            $FooStub,
        ]));
    }

    /**
     * @param string $namespace
     *
     * @return FooStub
     */
    public function buildFoo($namespace = 'Everon\Component\Factory\Tests\Unit\Doubles')
    {
        return $this->buildWithEmptyConstructor('FooStub', $namespace);
    }

    /**
     * @param string $namespace
     *
     * @return BarStub
     */
    public function buildBar(GizzStub $GizzStub, $anotherArgument, array $data, $namespace = 'Everon\Component\Factory\Tests\Unit\Doubles')
    {
        return $this->buildWithConstructorParameters('BarStub', $namespace, $this->buildParameterCollection([
            $GizzStub,
            $anotherArgument,
            $data
        ]));
    }

    public function buildGizz($namespace = 'Everon\Component\Factory\Tests\Unit\Doubles')
    {
        return $this->buildWithEmptyConstructor('GizzStub', $namespace);
    }
}
