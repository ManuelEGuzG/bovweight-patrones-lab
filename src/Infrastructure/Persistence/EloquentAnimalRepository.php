<?php

declare(strict_types=1);

namespace BovWeight\Lab\Infrastructure\Persistence;

use BovWeight\Lab\Domain\Animal\Animal;
use BovWeight\Lab\Domain\Animal\IAnimalRepository;
use BovWeight\Lab\Domain\Raza\IRazaFactory;

/**
 * Concrete Repository (Eloquent).
 *
 * En el proyecto Laravel real, esta clase usaría \App\Models\Animal::where(...)
 * y la lógica del ORM. Aquí simulamos esa interacción con un array para
 * mantener el laboratorio ejecutable sin BD.
 *
 * Lo importante es la FORMA: el resto del sistema NO conoce esta clase,
 * solo conoce IAnimalRepository. Por eso podemos sustituirla por
 * DoctrineAnimalRepository o InMemoryAnimalRepository sin tocar el dominio.
 */
final class EloquentAnimalRepository implements IAnimalRepository
{
    /**
     * Simulamos la tabla `animales` de la BD.
     * @var array<string, array{arete:string, raza:string, rancho_id:int, nombre:?string}>
     */
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
        // Equivalente Eloquent: Animal::where('arete', $arete)->first()
        if (!isset($this->tablaSimulada[$arete])) {
            return null;
        }
        return $this->hidratar($this->tablaSimulada[$arete]);
    }

    public function findAllByRancho(int $ranchoId): array
    {
        // Equivalente Eloquent:
        //   Animal::where('rancho_id', $ranchoId)->with('registrosPeso')->get()
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

    /**
     * Reconstruye la entidad de dominio desde una fila de la BD.
     */
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
