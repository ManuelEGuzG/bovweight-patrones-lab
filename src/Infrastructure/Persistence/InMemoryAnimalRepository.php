<?php

declare(strict_types=1);

namespace BovWeight\Lab\Infrastructure\Persistence;

use BovWeight\Lab\Domain\Animal\Animal;
use BovWeight\Lab\Domain\Animal\IAnimalRepository;


final class InMemoryAnimalRepository implements IAnimalRepository
{
    /** @var array<string, Animal> */
    private array $animales = [];

    public function findByArete(string $arete): ?Animal
    {
        return $this->animales[$arete] ?? null;
    }

    public function findAllByRancho(int $ranchoId): array
    {
        return array_values(array_filter(
            $this->animales,
            fn (Animal $a) => $a->getRanchoId() === $ranchoId
        ));
    }

    public function save(Animal $animal): void
    {
        $this->animales[$animal->getArete()] = $animal;
    }

    public function delete(string $arete): void
    {
        unset($this->animales[$arete]);
    }

    
    public function count(): int
    {
        return count($this->animales);
    }
}
