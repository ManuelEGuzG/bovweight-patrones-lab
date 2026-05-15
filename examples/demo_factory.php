<?php

declare(strict_types=1);

require __DIR__ . '/../autoload.php';

use BovWeight\Lab\Domain\Raza\IRazaFactory;
use BovWeight\Lab\Infrastructure\Container\ServiceContainer;
use BovWeight\Lab\Infrastructure\Container\ServiceProvider;

echo "============================================================\n";
echo "  DEMO PATRÓN FACTORY METHOD - BovWeight CR\n";
echo "============================================================\n\n";

$c = new ServiceContainer();
ServiceProvider::register($c);

/** @var IRazaFactory $factory */
$factory = $c->make(IRazaFactory::class);

echo "Razas disponibles en el catálogo SENASA:\n";
foreach ($factory->razasDisponibles() as $nombre) {
    echo "  - {$nombre}\n";
}
echo "\n";

echo "Creando instancias via factory (sin usar 'new' en el cliente):\n";
foreach (['Brahman', 'Nelore', 'Angus'] as $nombreRaza) {
    $raza = $factory->create($nombreRaza);
    printf(
        "  [%s]  origen=%s   factorAjustePeso=%.2f   senasa=%s\n",
        $raza->getNombre(),
        $raza->getOrigen(),
        $raza->factorAjustePeso(),
        $raza->aprobadaPorSenasa() ? 'sí' : 'no'
    );
}
echo "\n";

echo "Intento crear raza no registrada:\n";
try {
    $factory->create('Holstein');
} catch (\InvalidArgumentException $e) {
    echo "  EXCEPCIÓN ESPERADA: {$e->getMessage()}\n";
}
echo "\n";

echo "Observación clave:\n";
echo "  El cliente solo conoce IRazaFactory. Cuando SENASA añadió Angus,\n";
echo "  agregamos una línea al mapa interno. Ningún controlador se modificó.\n";
