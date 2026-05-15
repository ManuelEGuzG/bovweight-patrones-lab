<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Strategies;

use BovWeight\Lab\Domain\Estimacion\IAlgoritmoEstimacion;
use BovWeight\Lab\Domain\Estimacion\ResultadoEstimacion;


final class AlgoritmoRegresionLineal implements IAlgoritmoEstimacion
{
    public function estaDisponible(): bool
    {
        return true; // Sólo cálculo local, siempre disponible.
    }

    public function ejecutar(array $datosEntrada): ResultadoEstimacion
    {
        $perimetroTorax = (float) ($datosEntrada['perimetro_torax_cm'] ?? 0.0);
        $longitudCuerpo = (float) ($datosEntrada['longitud_cuerpo_cm'] ?? 0.0);
        $factorRaza     = (float) ($datosEntrada['factor_raza'] ?? 1.0);

        // Fórmula simplificada estilo Schaeffer adaptada al laboratorio.
        $pesoBase = (($perimetroTorax ** 2) * $longitudCuerpo) / 10838.0;
        $peso = $pesoBase * $factorRaza;

        return new ResultadoEstimacion(
            pesoKg: round($peso, 2),
            confianzaPorcentaje: 80.0,
            metodoUsado: 'regresion_lineal'
        );
    }
}
