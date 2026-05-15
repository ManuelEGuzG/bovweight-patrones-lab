<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Observers;

use BovWeight\Lab\Domain\RegistroPeso\IRegistroPesoObserver;
use BovWeight\Lab\Domain\RegistroPeso\RegistroPeso;

/**
 * ConcreteObserver: dispara un webhook hacia SENASA para
 * mantener actualizado el registro nacional de trazabilidad.
 */
final class WebhookSenasa implements IRegistroPesoObserver
{
    /** @var array<int, array{arete:string, peso:float, fecha:string}> */
    public array $payloadsEnviados = [];

    public function onPesoRegistrado(RegistroPeso $registro): void
    {
        // En el sistema real: Http::post('https://senasa.go.cr/api/...', [...])
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
