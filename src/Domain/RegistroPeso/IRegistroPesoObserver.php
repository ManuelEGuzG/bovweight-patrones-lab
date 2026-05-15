<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\RegistroPeso;


interface IRegistroPesoObserver
{
    
    public function onPesoRegistrado(RegistroPeso $registro): void;

    
    public function nombre(): string;
}
