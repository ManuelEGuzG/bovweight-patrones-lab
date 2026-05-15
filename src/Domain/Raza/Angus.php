<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Raza;


final class Angus extends Raza
{
    public function __construct()
    {
        parent::__construct('Angus', 'Escocia / Taurino');
    }

    public function factorAjustePeso(): float
    {
        
        return 1.08;
    }

    public function aprobadaPorSenasa(): bool
    {
        return true;
    }
}
