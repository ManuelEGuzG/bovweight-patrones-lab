<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\RegistroPeso;

use DateTimeImmutable;


final class RegistroPeso
{
    public function __construct(
        private readonly string $arete,
        private readonly float $pesoKg,
        private readonly string $metodoUsado,
        private readonly DateTimeImmutable $fecha
    ) {}

    public function getArete(): string
    {
        return $this->arete;
    }

    public function getPesoKg(): float
    {
        return $this->pesoKg;
    }

    public function getMetodoUsado(): string
    {
        return $this->metodoUsado;
    }

    public function getFecha(): DateTimeImmutable
    {
        return $this->fecha;
    }
}
