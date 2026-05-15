<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Raza;

/**
 * ConcreteProduct: Raza Nelore.
 *
 * Raza cebuína de origen brasileño común en fincas de carne en Guanacaste.
 */
final class Nelore extends Raza
{
    public function __construct()
    {
        parent::__construct('Nelore', 'Brasil / Cebú');
    }

    public function factorAjustePeso(): float
    {
        return 1.03;
    }

    public function aprobadaPorSenasa(): bool
    {
        return true;
    }
}
