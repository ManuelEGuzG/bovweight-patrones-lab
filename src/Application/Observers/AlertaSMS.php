<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Observers;

use BovWeight\Lab\Domain\RegistroPeso\IRegistroPesoObserver;
use BovWeight\Lab\Domain\RegistroPeso\RegistroPeso;

/**
 * ConcreteObserver agregado POSTERIORMENTE para demostrar el principio
 * Open/Closed del patrón Observer.
 *
 * El equipo de operaciones pidió agregar alertas SMS al ganadero cuando
 * el peso registrado supera ciertos umbrales. Agregar esto NO requirió
 * modificar:
 *   - RegistroPesoSubject
 *   - Ningún otro observer existente
 *   - El servicio que orquesta el guardado
 *
 * Solo basta con suscribirlo desde el ServiceProvider de Laravel.
 */
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
