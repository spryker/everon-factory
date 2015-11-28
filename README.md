# everon-factory
Beta version

# Everon Factory v0.5
Library to handle dependency injection and instantiation. Allows to produce code that is easy to test.

## Requirements
* Php 5.5+

## Features
* One line, lazy loaded dependency injection (via setters or constructors)
* Trait based, simple to use and implement, no hassle with config files, pure PHP
* Factory/FactoryWorker gives full control and overview, on how each and every object is created
* FactoryWorker allows for custom implementation of Factory methods
* Full control when a dependency should be reused or created from scratch, with Dependency Container (eg. Logger vs Value Object)
* Minimized file/memory access/usage due to callbacks and lazy load
* Intuitive Interface: clear, small and simple API
* Convention over configuration
* Clean code

## How it works
Every instantiation should happen only inside of the ```FactoryWorker``` class.
What it means, is that almost every usage of ```new``` operator should happen there.
It's easy to implement and manage, because injection is cheap and dependency setup happens in one place.

This makes testing much easier, as everything can be easily mocked/stubbed/faked.
It's ok to use ```new``` operator outside of Factory methods for simple value object like classes.

For a particular module or application, the ```registerBeforeWork``` method  of ```FactoryWorker``` is one of a places where you could setup your dependency tree.
Or you could use [Root Composition pattern](http://blog.ploeh.dk/2011/07/28/CompositionRoot/) and handle your whole dependency graph outside of your application.

### Easy Dependency Injection
To use dependency injection, register it with ```Dependency Container``` and use one line trait to inject it.

For example, this will inject a predefined ```Logger``` instance via setter injection into ```Foo``` class.
```php
class Foo
{
    use Dependency\Setter\Logger;
}
```

### Build with FactoryWorker
To build your dependencies use the ```FactoryWorker``` classes.


```php
class MyApplicationFactoryWorker extends AbstractWorker implements FactoryFactoryWorkerInterface
{
    /**
     * @inheritdoc
     */
    protected function registerBeforeWork()
    {
        $Factory = $this->getFactory();
        $this->getFactory()->getDependencyContainer()->propose('MyApplicationFactoryWorker', function () use ($Factory) {
            return $Factory->getWorkerByName('MyApplication', 'MyApplication\Modules\Logger\Factory');
        });
    }

    /**
     * @param string $namespace
     *
     * @return Foo
     */
    public function buildLogger($namespace = 'MyApplication\Modules\Logger')
    {
        return $this->buildWithEmptyConstructor('Logger', $namespace);
    }
}

```

### Register with Dependency Container
Use ```register``` method to register Logger dependency under ```Logger``` name.

```php
$Container->register('Logger', function () use ($FactoryWorker) {
    return $FactoryWorker->buildLogger();
});
```

### Define the traits and interfaces
Example of ```Logger``` dependency trait, which is reused between all of the classes that had ```Logger``` injected as dependency.
The only thing to remember is that, the name of the trait should be the same,
as the name under which the dependency was registered with the ```Dependency Container```.


```php
trait Logger
{
    /**
     * @var LoggerInterface
     */
    protected $Logger;

    /**
     * @inheritdoc
     */
    public function getLogger()
    {
        return $this->Logger;
    }

    /**
     * @inheritdoc
     */
    public function setLogger(LoggerInterface $Logger)
    {
        $this->Logger = $Logger;
    }
}
```

You can also define and assign the ```LoggerDependencyInterface``` too all classes that are being injected with ```Logger``` instance.
```php
interface LoggerDependencyInterface
{
    /**
     * @return LoggerInterface
     */
    public function getLogger();

    /**
     * @param Logger LoggerInterface
     */
    public function setLogger(LoggerInterface $Logger);
}
```

Define the setter injection trait.
The only requirement is that the name ends with ```Dependency\Setter\<dependency name>```.
You can reuse already defined ```Dependency\Logger``` trait for the setters and getters implementation.

```php
namespace MyApplication\Modules\Logger\Dependency\Setter;

use MyApplication\Modules\Logger\Dependency;

trait Logger
{
    use Dependency\Logger;
}
```


### Resolve with Dependency Container
Use ```resolve``` to receive dependency defined earlier with ```register``` or ```propose```.
So you can pass the same instance to another class via constructor injection.


```php
$Container->register('Bar', function () use ($FactoryWorker) {
    $Logger = $FactoryWorker->getFactory()->getDependencyContainer()->resolve('Logger');
    return $FactoryWorker->buildBar($Logger, 'argument', [
        'some' => 'data',
    ]);
});
```

### Result
Every ```Bar``` class will be injected with ```Logger``` instance, that was registered with the ```Dependency Container``` and build in ```FactoryWorker```.

```php
$Bar->getLogger()->log('It works');
```


### Dependency Container, Factory and FactoryWorker
Instantiate new ```Dependency Container``` and assign it to ```Factory```.
Use ```Factory``` to get instance of your specific ```FactoryWorker```.

```php
$Container = new Dependency\Container();
$Factory = new Factory($Container);
$FactoryWorker = $Factory->getWorkerByName('MyApplicationWorker', 'MyApplication\Modules\Logger\Factory');
```

### What's the best way to inject dependencies?
Use constructor for dependencies that are part of what the class is doing, and use setters/getters for infrastructure type dependencies.
In general, a ```Logger``` or ```FactoryWorker``` could be good examples of infrastructure type dependencies.

## Test Driven
See [tests](https://github.com/oliwierptak/everon-factory/blob/development/tests/unit/)
for [more examples with trait dependencies](https://github.com/oliwierptak/everon-factory/tree/development/tests/unit/doubles/).
