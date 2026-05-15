<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Observers;

use BovWeight\Lab\Domain\RegistroPeso\IRegistroPesoObserver;
use BovWeight\Lab\Domain\RegistroPeso\RegistroPeso;


final class WebhookSenasa implements IRegistroPesoObserver
{
    public array $payloadsEnviados = [];

    public function onPesoRegistrado(RegistroPeso $registro): void
    {
        
        $this->payloadsEnviados[] = [
            'arete' => $registro->getArete(),
            'peso'  => $registro->getPesoKg(),
            'fecha' => $registro->getFecha()->format(DATE_ATOM),
        ];
    }

    public function nombre(): string
    {
        return 'WebhookSenasa';
    }
}
