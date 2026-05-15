<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Raza;


abstract class Raza
{
    public function __construct(
        protected readonly string $nombre,
        protected readonly string $origen
    ) {}

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getOrigen(): string
    {
        return $this->origen;
    }

    /**
     * Factor de ajuste de peso específico por raza.
     * El modelo de IA usa este factor para corregir la estimación
     * en función de la conformación corporal de la raza.
     */
    abstract public function factorAjustePeso(): float;

    /**
     * Indica si la raza está aprobada por SENASA para el programa
     * de mejoramiento genético en Costa Rica.
     */
    abstract public function aprobadaPorSenasa(): bool;
}
