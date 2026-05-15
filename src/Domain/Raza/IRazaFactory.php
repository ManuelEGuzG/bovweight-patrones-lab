<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Raza;

/**
 * Creator (interfaz) en el patrón Factory Method.
 *
 * Define el contrato que el código cliente conoce. Los controladores
 * de Laravel (RegistroController, AnimalController, etc.) dependerán
 * SOLO de esta interfaz, no de las clases concretas.
 */
interface IRazaFactory
{
    /**
     * Crea una instancia de Raza a partir del nombre.
     *
     * @throws \InvalidArgumentException si la raza no está registrada.
     */
    public function create(string $nombreRaza): Raza;

    /**
     * Devuelve la lista de razas disponibles. Útil para poblar combos
     * en la UI sin tener que tocar este servicio cuando se agrega una raza.
     *
     * @return string[]
     */
    public function razasDisponibles(): array;
}
