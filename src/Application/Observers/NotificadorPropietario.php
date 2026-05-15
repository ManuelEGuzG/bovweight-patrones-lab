<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Observers;

use BovWeight\Lab\Domain\RegistroPeso\IRegistroPesoObserver;
use BovWeight\Lab\Domain\RegistroPeso\RegistroPeso;


final class NotificadorPropietario implements IRegistroPesoObserver
{
   
    public array $emailsEnviados = [];

    public function onPesoRegistrado(RegistroPeso $registro): void
    {
        $mensaje = sprintf(
            "Email -> Propietario del animal %s: nuevo pesaje %.2f kg (%s)",
            $registro->getArete(),
            $registro->getPesoKg(),
            $registro->getFecha()->format('Y-m-d H:i')
        );
        $this->emailsEnviados[] = $mensaje;
    }

    public function nombre(): string
    {
        return 'NotificadorPropietario';
    }
}
