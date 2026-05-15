<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Strategies;

use BovWeight\Lab\Domain\Estimacion\IAlgoritmoEstimacion;
use BovWeight\Lab\Domain\Estimacion\ResultadoEstimacion;


final class AlgoritmoTablaReferencia implements IAlgoritmoEstimacion
{
    
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
