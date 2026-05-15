<?php

declare(strict_types=1);

namespace BovWeight\Lab\Infrastructure\Container;

use BovWeight\Lab\Application\Strategies\AlgoritmoYolov8;
use BovWeight\Lab\Domain\Animal\IAnimalRepository;
use BovWeight\Lab\Domain\Estimacion\IAlgoritmoEstimacion;
use BovWeight\Lab\Domain\Raza\IRazaFactory;
use BovWeight\Lab\Domain\Raza\RazaFactory;
use BovWeight\Lab\Domain\RegistroPeso\RegistroPesoSubject;
use BovWeight\Lab\Infrastructure\Persistence\EloquentAnimalRepository;

/**
 * ServiceProvider equivalente al AppServiceProvider de Laravel.
 *
 * Centraliza el "cableado" (wiring) de los patrones para que el código
 * cliente reciba dependencias por inyección, no las cree con `new`.
 */
final class ServiceProvider
{
    public static function register(ServiceContainer $c): void
    {
        // Patrón Factory - RazaFactory como singleton.
        $c->singleton(IRazaFactory::class, fn () => new RazaFactory());

        // Patrón Repository - EloquentAnimalRepository por defecto.
        $c->singleton(
            IAnimalRepository::class,
            fn (ServiceContainer $c) => new EloquentAnimalRepository(
                $c->make(IRazaFactory::class)
            )
        );

        // Patrón Observer - el Subject como singleton para que todos
        // los suscriptores se conecten al mismo agregador.
        $c->singleton(RegistroPesoSubject::class, fn () => new RegistroPesoSubject());

        // Patrón Strategy - algoritmo por defecto YOLOv8.
        // En tiempo de ejecución se puede sustituir.
        $c->bind(IAlgoritmoEstimacion::class, fn () => new AlgoritmoYolov8(true));
    }
}
