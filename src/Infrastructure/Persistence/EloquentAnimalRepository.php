<?php

declare(strict_types=1);

namespace BovWeight\Lab\Infrastructure\Persistence;

use BovWeight\Lab\Domain\Animal\Animal;
use BovWeight\Lab\Domain\Animal\IAnimalRepository;
use BovWeight\Lab\Domain\Raza\IRazaFactory;


final class EloquentAnimalRepository implements IAnimalRepository
{
    
    private array $tablaSimulada = [];

    public function __construct(private readonly IRazaFactory $razaFactory)
    {
        // Datos sembrados para la demo (en Eloquent serían filas reales).
        $this->tablaSimulada = [
            'CR-0001' => ['arete' => 'CR-0001', 'raza' => 'brahman', 'rancho_id' => 1, 'nombre' => 'Lucero'],
            'CR-0002' => ['arete' => 'CR-0002', 'raza' => 'nelore',  'rancho_id' => 1, 'nombre' => null],
            'CR-0003' => ['arete' => 'CR-0003', 'raza' => 'brahman', 'rancho_id' => 2, 'nombre' => 'Estrella'],
        ];
    }

    public function findByArete(string $arete): ?Animal
    {
       
        if (!isset($this->tablaSimulada[$arete])) {
            return null;
        }
        return $this->hidratar($this->tablaSimulada[$arete]);
    }

    public function findAllByRancho(int $ranchoId): array
    {
        
        $resultado = [];
        foreach ($this->tablaSimulada as $fila) {
            if ($fila['rancho_id'] === $ranchoId) {
                $resultado[] = $this->hidratar($fila);
            }
        }
        return $resultado;
    }

    public function save(Animal $animal): void
    {
        // Equivalente Eloquent: $animal->save()
        $this->tablaSimulada[$animal->getArete()] = [
            'arete'     => $animal->getArete(),
            'raza'      => strtolower($animal->getRaza()->getNombre()),
            'rancho_id' => $animal->getRanchoId(),
            'nombre'    => $animal->getNombre(),
        ];
    }

    public function delete(string $arete): void
    {
        unset($this->tablaSimulada[$arete]);
    }

    
    private function hidratar(array $fila): Animal
    {
        return new Animal(
            arete:    $fila['arete'],
            raza:     $this->razaFactory->create($fila['raza']),
            ranchoId: $fila['rancho_id'],
            nombre:   $fila['nombre']
        );
    }
}
