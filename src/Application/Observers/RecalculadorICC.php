<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Observers;

use BovWeight\Lab\Domain\RegistroPeso\IRegistroPesoObserver;
use BovWeight\Lab\Domain\RegistroPeso\RegistroPeso;


final class RecalculadorICC implements IRegistroPesoObserver
{
    
    public array $iccCalculados = [];

    public function onPesoRegistrado(RegistroPeso $registro): void
    {
        $icc = min(5.0, max(1.0, $registro->getPesoKg() / 100.0));
        $this->iccCalculados[$registro->getArete()] = round($icc, 2);
    }

    public function nombre(): string
    {
        return 'RecalculadorICC';
    }
}
