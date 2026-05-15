<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Services;

use BovWeight\Lab\Domain\Estimacion\IAlgoritmoEstimacion;
use BovWeight\Lab\Domain\Estimacion\ResultadoEstimacion;

/**
 * Context del patrón Strategy.
 *
 * ANTES (sin patrón):
 *   if ($metodo === 'yolov8') { ... }
 *   elseif ($metodo === 'regresion') { ... }
 *   elseif ($metodo === 'tabla') { ... }
 *
 * AHORA (con patrón):
 *   $resultado = $this->algoritmo->ejecutar($datos);
 *
 * El método estimar() ya NO conoce los algoritmos concretos.
 * Cambiar de algoritmo en tiempo de ejecución es trivial via setAlgoritmo().
 */
final class EstimadorPesoService
{
    public function __construct(
        private IAlgoritmoEstimacion $algoritmo
    ) {}

    /**
     * Permite cambiar el algoritmo en tiempo de ejecución
     * (útil para implementar fallback YOLOv8 -> Tabla).
     */
    public function setAlgoritmo(IAlgoritmoEstimacion $algoritmo): void
    {
        $this->algoritmo = $algoritmo;
    }

    /**
     * Ejecuta el algoritmo inyectado. CERO if-else por método.
     */
    public function estimar(array $datosEntrada): ResultadoEstimacion
    {
        return $this->algoritmo->ejecutar($datosEntrada);
    }

    /**
     * Estima con fallback automático.
     * Si el algoritmo primario no está disponible (sin internet),
     * usa el secundario sin cambiar la firma de la operación.
     */
    public function estimarConFallback(
        array $datosEntrada,
        IAlgoritmoEstimacion $fallback
    ): ResultadoEstimacion {
        if (!$this->algoritmo->estaDisponible()) {
            return $fallback->ejecutar($datosEntrada);
        }
        return $this->algoritmo->ejecutar($datosEntrada);
    }
}
