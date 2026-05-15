<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Observers;

use BovWeight\Lab\Domain\RegistroPeso\IRegistroPesoObserver;
use BovWeight\Lab\Domain\RegistroPeso\RegistroPeso;

/**
 * ConcreteObserver: envía email al propietario del animal.
 *
 * En el sistema real haría un dispatch a la cola de Laravel; aquí
 * lo registramos en un buffer para que la demo y los tests lo verifiquen.
 */
final class NotificadorPropietario implements IRegistroPesoObserver
{
    /** @var string[] */
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
