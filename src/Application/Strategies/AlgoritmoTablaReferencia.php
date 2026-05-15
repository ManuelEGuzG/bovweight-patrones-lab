<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Strategies;

use BovWeight\Lab\Domain\Estimacion\IAlgoritmoEstimacion;
use BovWeight\Lab\Domain\Estimacion\ResultadoEstimacion;

/**
 * ConcreteStrategy: estimación por tabla de referencia.
 *
 * Es el algoritmo MÁS BÁSICO. Usa el rango de edad + raza contra una
 * tabla estática. No requiere internet ni cálculos complejos.
 *
 * Usado como FALLBACK cuando YOLOv8 no está disponible (potreros sin
 * señal mencionados por Don Alonso Chavarría).
 */
final class AlgoritmoTablaReferencia implements IAlgoritmoEstimacion
{
    /**
     * Tabla simplificada: peso promedio por meses de edad.
     * @var array<int, float>
     */
    private const TABLA_PESO_POR_MES = [
        6  => 180.0,
        12 => 260.0,
        18 => 340.0,
        24 => 420.0,
        36 => 480.0,
    ];

    public function estaDisponible(): bool
    {
        return true; // Disponible siempre (cálculo offline).
    }

    public function ejecutar(array $datosEntrada): ResultadoEstimacion
    {
        $edadMeses  = (int)   ($datosEntrada['edad_meses'] ?? 12);
        $factorRaza = (float) ($datosEntrada['factor_raza'] ?? 1.0);

        $pesoBase = $this->buscarEnTabla($edadMeses);
        $peso = $pesoBase * $factorRaza;

        return new ResultadoEstimacion(
            pesoKg: round($peso, 2),
            confianzaPorcentaje: 65.0,
            metodoUsado: 'tabla_referencia'
        );
    }

    /**
     * Devuelve el peso base correspondiente al tramo de edad más cercano.
     */
    private function buscarEnTabla(int $edadMeses): float
    {
        $tramoSeleccionado = 12;
        foreach (array_keys(self::TABLA_PESO_POR_MES) as $tramo) {
            if ($edadMeses >= $tramo) {
                $tramoSeleccionado = $tramo;
            }
        }
        return self::TABLA_PESO_POR_MES[$tramoSeleccionado];
    }
}
