<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Strategies;

use BovWeight\Lab\Domain\Estimacion\IAlgoritmoEstimacion;
use BovWeight\Lab\Domain\Estimacion\ResultadoEstimacion;
use RuntimeException;

/**
 * ConcreteStrategy: estimación basada en el modelo YOLOv8.
 *
 * Simula una llamada HTTP a un servicio de visión por computadora.
 * Es el método más preciso pero requiere conexión a internet.
 */
final class AlgoritmoYolov8 implements IAlgoritmoEstimacion
{
    public function __construct(private readonly bool $hayConexionInternet = true)
    {
    }

    public function estaDisponible(): bool
    {
        return $this->hayConexionInternet;
    }

    public function ejecutar(array $datosEntrada): ResultadoEstimacion
    {
        if (!$this->estaDisponible()) {
            throw new RuntimeException(
                'YOLOv8 no disponible: sin conexión al servicio de visión por computadora.'
            );
        }

        // Simulación de la llamada HTTP al servicio de IA.
        $pixelesLargo = (int) ($datosEntrada['pixeles_largo'] ?? 0);
        $factorRaza   = (float) ($datosEntrada['factor_raza'] ?? 1.0);

        // Fórmula simulada: el modelo real devolvería un peso vía API.
        $peso = ($pixelesLargo * 0.85) * $factorRaza;

        return new ResultadoEstimacion(
            pesoKg: round($peso, 2),
            confianzaPorcentaje: 92.5,
            metodoUsado: 'yolov8'
        );
    }
}
