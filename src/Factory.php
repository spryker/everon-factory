<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <EveronFramework@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory;

use Everon\Component\Factory\Dependency\ContainerInterface;
use Everon\Component\Factory\Dependency\FactoryDependencyInterface;
use Everon\Component\Factory\Exception\MissingFactoryDependencyInterfaceException;
use Everon\Component\Factory\Exception\UndefinedClassException;

class Factory implements FactoryInterface
{

    /**
     * @var ContainerInterface
     */
    protected $DependencyContainer = null;


    /**
     * @param ContainerInterface $Container
     */
    public function __construct(ContainerInterface $Container)
    {
        $this->DependencyContainer = $Container;
    }

    /**
     * @inheritdoc
     */
    public function injectDependencies($class_name, $Instance)
    {
        $this->getDependencyContainer()->inject($Instance);
        if ($this->getDependencyContainer()->isFactoryRequired($class_name)) {
            if (($Instance instanceof FactoryDependencyInterface) === false) {
                throw new MissingFactoryDependencyInterfaceException($class_name);
            }
            /** @var FactoryDependencyInterface $Instance */
            $Instance->setFactory($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function getDependencyContainer()
    {
        return $this->DependencyContainer;
    }

    /**
     * @inheritdoc
     */
    public function setDependencyContainer(ContainerInterface $Container)
    {
        $this->DependencyContainer = $Container;
    }

    /**
     * @inheritdoc
     */
    public function getFullClassName($namespace, $class_name)
    {
        if ($class_name[0] === '\\') { //used for when laading classmap from cache
            return $class_name; //absolute name
        }

        return $namespace.'\\'.$class_name;
    }

    /**
     * @inheritdoc
     */
    public function classExists($class)
    {
        if (class_exists($class, true) === false) {
            throw new UndefinedClassException($class);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildDateTime($time='now', \DateTimeZone $timezone=null)
    {
        return new \DateTime($time, $timezone);
    }

    /**
     * @inheritdoc
     */
    public function buildDateTimeZone($timezone)
    {
        return new \DateTimeZone($timezone);
    }

    /**
     * @inheritdoc
     */
    public function buildIntlDateFormatter($locale, $datetype, $timetype, $timezone, $calendar, $pattern)
    {
        return new \IntlDateFormatter($locale, $datetype, $timetype, $timezone, $calendar, $pattern);
    }
}
