<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Raza;


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
