<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Services;

use BovWeight\Lab\Domain\Estimacion\IAlgoritmoEstimacion;
use BovWeight\Lab\Domain\Estimacion\ResultadoEstimacion;


final class EstimadorPesoService
{
    public function __construct(
        private IAlgoritmoEstimacion $algoritmo
    ) {}

    
    public function setAlgoritmo(IAlgoritmoEstimacion $algoritmo): void
    {
        $this->algoritmo = $algoritmo;
    }

    
    public function estimar(array $datosEntrada): ResultadoEstimacion
    {
        return $this->algoritmo->ejecutar($datosEntrada);
    }

    
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
