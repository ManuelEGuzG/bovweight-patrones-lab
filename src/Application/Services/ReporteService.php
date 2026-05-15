<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Services;

use BovWeight\Lab\Domain\Animal\IAnimalRepository;

/**
 * ReporteService refactorizado para usar el patrón Repository.
 *
 * ANTES (sin patrón):
 *   $animales = Animal::where('rancho_id', $id)->with('registrosPeso')->get();
 *
 * AHORA (con patrón):
 *   $animales = $this->animalRepository->findAllByRancho($id);
 *
 * Beneficios obtenidos:
 *   1. Si cambiamos a Doctrine, esta clase NO se toca.
 *   2. Si agregamos cache Redis, se hace en un solo lugar (el repo).
 *   3. Los tests usan InMemoryAnimalRepository: ejecutan en milisegundos.
 *
 * Este reporte es el que pidió Don Diego Chavarría (comprador de ganado)
 * para negociar precios sin necesidad de báscula.
 */
final class ReporteService
{
    public function __construct(
        private readonly IAnimalRepository $animalRepository
    ) {}

    /**
     * Genera el reporte de animales disponibles para venta en un rancho.
     *
     * @return array<int, array{arete:string, raza:string, nombre:?string}>
     */
    public function reporteAnimalesPorRancho(int $ranchoId): array
    {
        $animales = $this->animalRepository->findAllByRancho($ranchoId);

        $reporte = [];
        foreach ($animales as $animal) {
            $reporte[] = [
                'arete'  => $animal->getArete(),
                'raza'   => $animal->getRaza()->getNombre(),
                'nombre' => $animal->getNombre(),
            ];
        }
        return $reporte;
    }
}
