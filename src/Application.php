<?php

namespace Aspect\Lib;

use Aspect\Lib\Blueprint\Ignore;
use Aspect\Lib\Blueprint\Pretty;
use Aspect\Lib\DI\Factory;
use Aspect\Lib\DI\Fake;
use Aspect\Lib\Event\Repository;
use Aspect\Lib\Service\DI\Factory as Kernel;
use Aspect\Lib\DI\Invoke\FunctionInvoker;
use Aspect\Lib\DI\Invoke\ObjectBuilder;
use Aspect\Lib\DI\Locator;
use Aspect\Lib\Facade\Yakov;
use Aspect\Lib\Helper\ClassLoader;
use Aspect\Lib\Struct\PrettyPrint;
use Aspect\Lib\Struct\Singleton;
use Aspect\Lib\Support\Interfaces\RuntimeInterface;
use function Aspect\Lib\DI\factory;
use function Aspect\Lib\DI\object;

final class Application
{
    use Singleton;
    use PrettyPrint;

    #[Pretty]
    private Gateway $gateway = Gateway::UNKNOWN;

    #[Pretty]
    private Context $context = Context::WEB;

    private DI\Container $container;

    #[Ignore]
    private Fake $fake;

    #[Ignore]
    protected function make(): ?callable
    {
        if (defined('ASPECT_INIT_CONTEXT')) {
            $this->context = Context::tryFrom(ASPECT_INIT_CONTEXT);
        }

        $this->gateway = Gateway::detect();

        $locator = new Locator();
        $this->applyFactories($locator);

        $this->container = new DI\Container($locator);
        $this->fake = new Fake($locator);


        $runtimeClasses = (new ClassLoader())->getClassesFromYakov(
            Yakov::getPathToRuntime(),
            fn ($className) => is_a($className, RuntimeInterface::class, true)
        );

        $runtimes = array_map(static fn ($className) => new $className(), $runtimeClasses);

        /** @var RuntimeInterface $runtime */
        foreach ($runtimes as $runtime) {
            $runtime->onBitrixLoaded();
        }

        return static function () use ($runtimes) {
            $self = Application::getInstance();
            /** @var RuntimeInterface $runtime */
            foreach ($runtimes as $runtime) {
                $runtime->onReady($self);
            }
        };
    }

    #[Ignore]
    protected function applyFactories(Locator $locator): void
    {
        $entities = [];
        foreach ($this->collectFactoryBinds() as $factoryClass) {
            /** @var Kernel $factory */
            $factory = new $factoryClass($this);
            $entities[] = $factory->bind();
        }

        $entities[] = include Yakov::getFactoryPath();

        $entities = array_merge(...$entities);

        foreach ($entities as $key => $entity) {
            if (is_a($entity, Factory::class, true)) {
                $locator->addFactory($key, $entity);
            } else if (is_callable($entity)) {
                $locator->addFactory($key, factory($entity));
            } else {
                $locator->addFactory($key, object($entity));
            }
        }
    }

    #[Ignore]
    protected function collectFactoryBinds(): array
    {
        return (new ClassLoader())->getClassesFromYakov(
            Yakov::getPathToFactories(),
            fn($className) => class_implements($className, Kernel::class)
        );
    }

    #[Ignore]
    public function gateway(): Gateway
    {
        return $this->gateway;
    }

    #[Ignore]
    public function context(): Context
    {
        return $this->context;
    }

    #[Ignore]
    public function fake(): Fake
    {
        return $this->fake;
    }

    /**
     * @throws \ReflectionException
     */
    public function bound(Context $context, callable $closure): mixed
    {
        $oldContext = $this->context;

        $this->context = $context;
        $result = $this->inject($closure);
        $this->context = $oldContext;

        return $result;
    }

    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    /**
     * @throws \ReflectionException
     */
    public function inject(callable|\Closure $closure, ?array $parameters = []): mixed
    {
        return FunctionInvoker::invoke(new \ReflectionFunction($closure), $this->container, null, $parameters);
    }

    /**
     * @throws \ReflectionException
     */
    public function injectMethod(\ReflectionMethod $closure, object $object, ?array $parameters = []): mixed
    {
        return FunctionInvoker::invoke($closure, $this->container, $object, $parameters);
    }

    /**
     * @throws \Exception
     */
    public function fetchTo($object): void
    {
        ObjectBuilder::injectPropertyDependencies($this->container, $object);
    }

    /**
     * @template T
     * @param string|class-string<T> $interface
     * @return T
     * @throws \Exception
     */
    public function get(string $interface)
    {
        $resolver = $this->container->get($interface);
        if (is_string($resolver) && class_exists($resolver)) {
            return $this->get($resolver);
        }
        return $resolver;
    }

    public function isInjectable(string $interface): bool
    {
        return $this->container->has($interface);
    }
}