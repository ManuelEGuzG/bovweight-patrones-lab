<?php

declare(strict_types=1);

require __DIR__ . '/../autoload.php';

use BovWeight\Lab\Application\Services\ReporteService;
use BovWeight\Lab\Domain\Animal\Animal;
use BovWeight\Lab\Domain\Animal\IAnimalRepository;
use BovWeight\Lab\Domain\Raza\IRazaFactory;
use BovWeight\Lab\Infrastructure\Container\ServiceContainer;
use BovWeight\Lab\Infrastructure\Container\ServiceProvider;
use BovWeight\Lab\Infrastructure\Persistence\InMemoryAnimalRepository;

echo "============================================================\n";
echo "  DEMO PATRÓN REPOSITORY - BovWeight CR\n";
echo "============================================================\n\n";

$c = new ServiceContainer();
ServiceProvider::register($c);

/** @var IAnimalRepository $repo */
$repo = $c->make(IAnimalRepository::class);
$reporte = new ReporteService($repo);

echo "1) Usando EloquentAnimalRepository (datos sembrados):\n";
echo "   Reporte de animales del rancho 1 (Finca La Esperanza):\n";
foreach ($reporte->reporteAnimalesPorRancho(1) as $fila) {
    printf(
        "     arete=%-8s raza=%-10s nombre=%s\n",
        $fila['arete'],
        $fila['raza'],
        $fila['nombre'] ?? '(sin nombre)'
    );
}
echo "\n";

echo "2) Cambio de implementación: usar InMemoryAnimalRepository\n";
echo "   (mismo ReporteService, no se tocó la lógica de negocio):\n";

$inMemory = new InMemoryAnimalRepository();
/** @var IRazaFactory $factory */
$factory = $c->make(IRazaFactory::class);

$inMemory->save(new Animal(
    arete:    'CR-9999',
    raza:     $factory->create('angus'),
    ranchoId: 7,
    nombre:   'Tornado'
));
$inMemory->save(new Animal(
    arete:    'CR-8888',
    raza:     $factory->create('nelore'),
    ranchoId: 7,
    nombre:   null
));

$reporteEnMemoria = new ReporteService($inMemory);
echo "   Reporte de animales del rancho 7 (datos en memoria):\n";
foreach ($reporteEnMemoria->reporteAnimalesPorRancho(7) as $fila) {
    printf(
        "     arete=%-8s raza=%-10s nombre=%s\n",
        $fila['arete'],
        $fila['raza'],
        $fila['nombre'] ?? '(sin nombre)'
    );
}
echo "\n";

echo "Observación clave:\n";
echo "  ReporteService no sabe si los datos vienen de Eloquent, Doctrine\n";
echo "  o un array en memoria. Solo depende de la INTERFAZ.\n";
echo "  Esto es lo que permite pruebas unitarias rápidas y migración de ORM.\n";
