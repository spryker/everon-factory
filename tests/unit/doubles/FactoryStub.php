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


use Everon\Component\Factory\Exception\MissingFactoryDependencyInterfaceException;
use Everon\Component\Factory\Exception\UndefinedClassException;
use Everon\Component\Factory\Factory;

class FactoryStub extends Factory
{
    /**
     * @param string $namespace
     *
     * @throws MissingFactoryDependencyInterfaceException
     * @throws UndefinedClassException
     * @return FuzzStub
     */
    public function buildFuzz($namespace='Everon\Component\Factory\Tests\Unit\Doubles')
    {
        $className = $this->getFullClassName($namespace, 'FuzzStub');
        $this->classExists($className);

        $Fuzz = new $className;
        $this->injectDependencies($className, $Fuzz);

        return $Fuzz;
    }

    /**
     * @param string $namespace
     *
     * @throws UndefinedClassException
     * @throws MissingFactoryDependencyInterfaceException
     * @return FooStub
     */
    public function buildFoo($namespace='Everon\Component\Factory\Tests\Unit\Doubles')
    {
        $className = $this->getFullClassName($namespace, 'FooStub');
        $this->classExists($className);

        $Foo = new $className;
        $this->injectDependencies($className, $Foo);

        return $Foo;
    }

    /**
     * @param string $namespace
     *
     * @throws UndefinedClassException
     * @throws MissingFactoryDependencyInterfaceException
     * @return BarStub
     */
    public function buildBar($namespace='Everon\Component\Factory\Tests\Unit\Doubles')
    {
        $className = $this->getFullClassName($namespace, 'BarStub');
        $this->classExists($className);

        $Bar = new $className;
        $this->injectDependencies($className, $Bar);

        return $Bar;
    }
}
