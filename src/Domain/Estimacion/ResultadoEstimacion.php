<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Estimacion;

/**
 * Value Object inmutable: resultado de una estimación de peso.
 *
 * Propiedades readonly (sin setters). Conforme al laboratorio:
 *   - pesoKg               (float)
 *   - confianzaPorcentaje  (float)
 *   - metodoUsado          (string)
 *
 * La inmutabilidad garantiza que ningún consumidor del resultado pueda
 * alterar los valores devueltos por el algoritmo.
 */
final class ResultadoEstimacion
{
    public function __construct(
        public readonly float $pesoKg,
        public readonly float $confianzaPorcentaje,
        public readonly string $metodoUsado
    ) {}

    public function __toString(): string
    {
        return sprintf(
            "%.2f kg (confianza %.1f%%) - método: %s",
            $this->pesoKg,
            $this->confianzaPorcentaje,
            $this->metodoUsado
        );
    }
}
