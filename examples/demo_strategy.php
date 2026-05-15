<?php

declare(strict_types=1);

require __DIR__ . '/../autoload.php';

use BovWeight\Lab\Application\Services\EstimadorPesoService;
use BovWeight\Lab\Application\Strategies\AlgoritmoRegresionLineal;
use BovWeight\Lab\Application\Strategies\AlgoritmoTablaReferencia;
use BovWeight\Lab\Application\Strategies\AlgoritmoYolov8;
use BovWeight\Lab\Domain\Raza\IRazaFactory;
use BovWeight\Lab\Infrastructure\Container\ServiceContainer;
use BovWeight\Lab\Infrastructure\Container\ServiceProvider;

echo "============================================================\n";
echo "  DEMO PATRÓN STRATEGY - BovWeight CR\n";
echo "============================================================\n\n";

$c = new ServiceContainer();
ServiceProvider::register($c);

/** @var IRazaFactory $factory */
$factory = $c->make(IRazaFactory::class);
$brahman = $factory->create('brahman');

$datos = [
    'pixeles_largo'      => 480,
    'perimetro_torax_cm' => 178,
    'longitud_cuerpo_cm' => 145,
    'edad_meses'         => 24,
    'factor_raza'        => $brahman->factorAjustePeso(),
];

echo "Animal: Brahman, factor de raza = {$brahman->factorAjustePeso()}\n\n";

// 1. YOLOv8 con conexión.
$yolo = new AlgoritmoYolov8(hayConexionInternet: true);
$service = new EstimadorPesoService($yolo);
$resultado = $service->estimar($datos);
echo "1) Algoritmo YOLOv8 (con conexión):\n";
echo "   {$resultado}\n\n";

// 2. Cambio en tiempo de ejecución -> Regresión Lineal.
$service->setAlgoritmo(new AlgoritmoRegresionLineal());
$resultado = $service->estimar($datos);
echo "2) Algoritmo Regresión Lineal (offline-friendly):\n";
echo "   {$resultado}\n\n";

// 3. Tabla de Referencia.
$service->setAlgoritmo(new AlgoritmoTablaReferencia());
$resultado = $service->estimar($datos);
echo "3) Algoritmo Tabla de Referencia (fallback básico):\n";
echo "   {$resultado}\n\n";

// 4. Fallback automático: simulamos que NO hay internet.
echo "4) FALLBACK AUTOMÁTICO (escenario potrero sin señal,\n";
echo "   caso descrito por Don Alonso Chavarría en el levantamiento):\n";
$yoloSinInternet = new AlgoritmoYolov8(hayConexionInternet: false);
$service = new EstimadorPesoService($yoloSinInternet);

$resultadoFallback = $service->estimarConFallback(
    $datos,
    new AlgoritmoTablaReferencia()
);
echo "   {$resultadoFallback}\n";
echo "   (Como YOLOv8 reportó no disponible, el servicio cambió\n";
echo "    a la Tabla de Referencia sin un solo if del lado del cliente.)\n\n";

echo "Observación clave:\n";
echo "  EstimadorPesoService no contiene NI UN solo if-else por algoritmo.\n";
echo "  La elección está encapsulada en clases independientes y se inyecta.\n";
