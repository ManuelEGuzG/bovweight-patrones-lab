<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Raza;

/**
 * ConcreteProduct: Raza Angus.
 *
 * Raza añadida POSTERIORMENTE al programa de SENASA. Demuestra que
 * gracias al Factory Method podemos extender el catálogo de razas
 * sin modificar el código cliente (principio Open/Closed).
 */
final class Angus extends Raza
{
    public function __construct()
    {
        parent::__construct('Angus', 'Escocia / Taurino');
    }

    public function factorAjustePeso(): float
    {
        // Conformación cárnica densa, ajuste +8%
        return 1.08;
    }

    public function aprobadaPorSenasa(): bool
    {
        return true;
    }
}
