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


use Everon\Component\Factory\Exception\UndefinedClassException;
use Everon\Component\Factory\Factory;

class FactoryStub extends Factory
{
    /**
     * @param string $namespace
     *
     * @throws UndefinedClassException
     * @return FuzzStub
     */
    public function buildFuzz($namespace='Everon\Component\Factory\Tests\Unit\Doubles')
    {
        $className = $this->getFullClassName($namespace, 'FuzzStub');
        $this->classExists($className);

        $class = new $className;

        return new $class;
    }
}
