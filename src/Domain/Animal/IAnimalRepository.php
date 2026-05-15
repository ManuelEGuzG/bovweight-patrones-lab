<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Animal;

/**
 * Repository Interface (patrón Repository - Fowler / DDD).
 *
 * Importante: los métodos hablan el lenguaje del dominio (findByArete,
 * findAllByRancho), no el lenguaje del ORM (where, with, get).
 *
 * El ReporteService, EstimadorService y DashboardController dependerán
 * de esta interfaz por inyección de dependencias, no de Eloquent.
 */
interface IAnimalRepository
{
    /**
     * Busca un animal por su número de arete SENASA.
     */
    public function findByArete(string $arete): ?Animal;

    /**
     * Lista todos los animales pertenecientes a un rancho.
     *
     * @return Animal[]
     */
    public function findAllByRancho(int $ranchoId): array;

    /**
     * Persiste un animal (crear o actualizar).
     */
    public function save(Animal $animal): void;

    /**
     * Elimina un animal por arete.
     */
    public function delete(string $arete): void;
}
