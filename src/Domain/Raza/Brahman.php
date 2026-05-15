<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Raza;


final class Brahman extends Raza
{
    public function __construct()
    {
        parent::__construct('Brahman', 'India / Cebú');
    }

    public function factorAjustePeso(): float
    {
        
        return 1.05;
    }

    public function aprobadaPorSenasa(): bool
    {
        return true;
    }
}
