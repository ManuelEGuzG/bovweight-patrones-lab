<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Animal;


interface IAnimalRepository
{
    
    public function findByArete(string $arete): ?Animal;

    /**
     * Lista todos los animales pertenecientes a un rancho.
     *
     * @return Animal[]
     */
    public function findAllByRancho(int $ranchoId): array;

    
    public function save(Animal $animal): void;

    
    public function delete(string $arete): void;
}
