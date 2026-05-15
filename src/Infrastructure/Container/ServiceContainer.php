<?php

declare(strict_types=1);

namespace BovWeight\Lab\Infrastructure\Container;

use Closure;
use RuntimeException;

/**
 * Service Container minimalista al estilo Laravel.
 *
 * Permite registrar bindings (interfaz -> implementación) y singletons.
 * En el proyecto Laravel real, esto es app() / $this->app->bind().
 *
 * Para el laboratorio, encapsula los bindings que pide cada patrón:
 *   - IRazaFactory          -> RazaFactory (singleton)
 *   - IAnimalRepository     -> EloquentAnimalRepository
 *   - IAlgoritmoEstimacion  -> AlgoritmoYolov8 (por defecto)
 */
final class ServiceContainer
{
    /** @var array<string, Closure> */
    private array $bindings = [];

    /** @var array<string, object> */
    private array $singletons = [];

    /** @var array<string, true> */
    private array $sharedFlags = [];

    public function bind(string $abstract, Closure $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    public function singleton(string $abstract, Closure $factory): void
    {
        $this->bindings[$abstract] = $factory;
        $this->sharedFlags[$abstract] = true;
    }

    /**
     * @template T of object
     * @param class-string<T> $abstract
     * @return T
     */
    public function make(string $abstract): object
    {
        if (isset($this->singletons[$abstract])) {
            /** @var T */
            return $this->singletons[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            throw new RuntimeException(
                "No hay binding registrado para '{$abstract}'."
            );
        }

        $instancia = ($this->bindings[$abstract])($this);

        if (isset($this->sharedFlags[$abstract])) {
            $this->singletons[$abstract] = $instancia;
        }

        /** @var T */
        return $instancia;
    }
}
