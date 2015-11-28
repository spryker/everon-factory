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

## How it works, what value does it provide
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

### Resolve with Dependency Container
Use ```resolve``` to receive dependency defined earlier with ```register``` or ```propose```.
So you can pass the same instance to another class via constructor injection.
Or you could just call ```$FactoryWorker->buildLogger()``` if you decided that every ```Bar``` instance should get
new instance of ```Logger``` class.

```php
$Container->register('Bar', function () use ($FactoryWorker) {
    $Logger = $FactoryWorker->getFactory()->getDependencyContainer()->resolve('Logger');
    return $FactoryWorker->buildBar($Logger, 'argument', [
        'some' => 'data',
    ]);
});
```

### Create Dependency Container, Factory and FactoryWorker
Instantiate new ```Dependency Container``` and assign it to ```Factory```.
Use Factory to get instance of your application or module with specific ```FactoryWorker```.

```php
$Container = new Dependency\Container();
$Factory = new Factory($Container);
$FactoryWorker = $Factory->getWorkerByName('MyApplicationWorker', 'MyApplication\Modules\Logger\Factory');
```
