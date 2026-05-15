<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Observers;

use BovWeight\Lab\Domain\RegistroPeso\IRegistroPesoObserver;
use BovWeight\Lab\Domain\RegistroPeso\RegistroPeso;


final class AlertaSMS implements IRegistroPesoObserver
{
    /** @var string[] */
    public array $smsEnviados = [];

    public function onPesoRegistrado(RegistroPeso $registro): void
    {
        if ($registro->getPesoKg() >= 500.0) {
            $this->smsEnviados[] = sprintf(
                "SMS -> Animal %s alcanzó %.2f kg, listo para venta.",
                $registro->getArete(),
                $registro->getPesoKg()
            );
        }
    }

    public function nombre(): string
    {
        return 'AlertaSMS';
    }
}
