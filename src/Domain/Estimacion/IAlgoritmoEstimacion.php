<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Estimacion;


interface IAlgoritmoEstimacion
{
   
    public function ejecutar(array $datosEntrada): ResultadoEstimacion;

    
    public function estaDisponible(): bool;
}
