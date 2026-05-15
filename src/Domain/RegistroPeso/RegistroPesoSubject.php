<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\RegistroPeso;


final class RegistroPesoSubject
{
    /** @var IRegistroPesoObserver[] */
    private array $observadores = [];

    public function suscribir(IRegistroPesoObserver $observer): void
    {
        
        foreach ($this->observadores as $existente) {
            if ($existente === $observer) {
                return;
            }
        }
        $this->observadores[] = $observer;
    }

    public function desuscribir(IRegistroPesoObserver $observer): void
    {
        $this->observadores = array_values(array_filter(
            $this->observadores,
            fn (IRegistroPesoObserver $o) => $o !== $observer
        ));
    }

    /** @return IRegistroPesoObserver[] */
    public function getObservadores(): array
    {
        return $this->observadores;
    }

    
    public function notificar(RegistroPeso $registro): void
    {
        foreach ($this->observadores as $observer) {
            $observer->onPesoRegistrado($registro);
        }
    }
}
