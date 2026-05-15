<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Services;

use BovWeight\Lab\Domain\Animal\IAnimalRepository;


final class ReporteService
{
    public function __construct(
        private readonly IAnimalRepository $animalRepository
    ) {}

    
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
