<?php
declare(strict_types=1);

namespace Common\Service;

use Phalcon\Di\Injectable as PhalconInjectable;
use Dice\Dice;
use ReflectionClass;
use ReflectionParameter;
use ReflectionException;
use Throwable;

class Injectable extends PhalconInjectable
{
    public function inject(string $class): object
    {
        return $this->resolveInjectionWithoutCache($class);
    }

    /**
     * Method resolves DI container autowiring by reading full path of all
     * dependencies starting from injected class to last class with empty
     * constructor.
     *
     * Some vendors classes can not be resolved and exception is thrown. In
     * this case class autowiring is resolved with Dice library which resolves
     * autowiring much better but can't be mocked in unit tests.
     * I hope to find time to resolve this problem in nearby future.
     *
     * @param string $class
     * @return object
     */
    private function resolveInjectionWithoutCache(string $class): object
    {
        try {
            $reflection = new ReflectionClass($class);

            $constructor = $reflection->getConstructor();

            if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
                return $this->di->get($class);
            }

            $constructorParameters = $constructor->getParameters();

            return $this->di->get(
                $class,
                $this->resolveClassParameters($constructorParameters)
            );
        } catch (Throwable $throwable) {
            // TODO: write better solution instead of using Dice in case of exception
            return $this->resolveInjectionWithDice($class);
        }
    }

    /**
     * @param ReflectionParameter[] $constructorParameters
     * @return mixed[]
     * @throws ReflectionException
     */
    private function resolveClassParameters(array $constructorParameters): array
    {
        if (count($constructorParameters) === 0) {
            return [];
        }

        $parameters = [];
        foreach ($constructorParameters as $constructorParameter) {
            if ($constructorParameter->getClass() === null) {
                $parameters[] = $constructorParameter->getDefaultValue();

                continue;
            }

            $parameters[] = $this->resolveInjectionWithoutCache(
                $constructorParameter->getClass()->getName()
            );
        }

        return $parameters;
    }

    /**
     * Dice is tiny but powerful DI container autowiring resolving library.
     * Unfortunately, it is incompatible with mocking (at least in Phalcon),
     * so it should be used only for resolving problematic classes that are
     * hard to resolve or resolving costs too much time in other ways.
     *
     * Note, that classes and all subclasses resolved with Dice can not be
     * mocked in unit tests, so use it with caution.
     *
     * @param string $class
     * @return object
     */
    private function resolveInjectionWithDice(string $class): object
    {
        return (new Dice())->create($class);
    }
}
