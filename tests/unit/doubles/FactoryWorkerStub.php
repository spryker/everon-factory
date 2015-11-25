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

use Everon\Component\Factory\AbstractWorker;

class FactoryWorkerStub extends AbstractWorker
{
    /**
     * @param string $namespace
     *
     * @return FuzzStub
     */
    public function buildFuzz($namespace = 'Everon\Component\Factory\Tests\Unit\Doubles')
    {
        return $this->getFactory()->buildWithEmptyConstructor('FuzzStub', $namespace);
    }

    /**
     * @param string $namespace
     *
     * @return FooStub
     */
    public function buildFoo($namespace = 'Everon\Component\Factory\Tests\Unit\Doubles')
    {
        return $this->getFactory()->buildWithEmptyConstructor('FooStub', $namespace);
    }

    /**
     * @param string $namespace
     *
     * @return BarStub
     */
    public function buildBar($anotherArgument, array $data, $namespace = 'Everon\Component\Factory\Tests\Unit\Doubles')
    {
        $GizzStub = $this->buildGizz($namespace);
        return $this->getFactory()->buildWithConstructorParameters('BarStub', $namespace,
            $this->getFactory()->buildParameterCollection([
                $GizzStub,
                $anotherArgument,
                $data
            ])
        );
    }

    /**
     * @param string $namespace
     *
     * @return GizzStub
     */
    public function buildGizz($namespace = 'Everon\Component\Factory\Tests\Unit\Doubles')
    {
        return $this->getFactory()->buildWithEmptyConstructor('GizzStub', $namespace);
    }

}
