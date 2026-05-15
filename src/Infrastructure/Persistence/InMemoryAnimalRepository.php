<?php

declare(strict_types=1);

namespace BovWeight\Lab\Infrastructure\Persistence;

use BovWeight\Lab\Domain\Animal\Animal;
use BovWeight\Lab\Domain\Animal\IAnimalRepository;

/**
 * In-Memory Repository.
 *
 * Implementación pensada exclusivamente para PRUEBAS UNITARIAS.
 * No toca BD: usa un array privado. Permite que ReporteServiceTest,
 * EstimadorServiceTest, etc. corran en milisegundos sin necesidad
 * de migrar BD ni levantar contenedores.
 *
 * Esta es una de las ventajas principales del patrón Repository
 * mencionadas por Fowler.
 */
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

    /**
     * Método auxiliar exclusivo para tests: cuenta animales almacenados.
     */
    public function count(): int
    {
        return count($this->animales);
    }
}
