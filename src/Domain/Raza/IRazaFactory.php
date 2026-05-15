<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Raza;


interface IRazaFactory
{
    /**
     * Crea una instancia de Raza a partir del nombre.
     *
     * @throws \InvalidArgumentException si la raza no está registrada.
     */
    public function create(string $nombreRaza): Raza;

    
    public function razasDisponibles(): array;
}
