<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\RegistroPeso;

/**
 * Observer (interfaz) en el patrón Observer GoF.
 *
 * Todo subsistema que quiera reaccionar ante un nuevo pesaje debe
 * implementar esta interfaz.
 */
interface IRegistroPesoObserver
{
    /**
     * Se invoca cuando se ha registrado un nuevo pesaje.
     */
    public function onPesoRegistrado(RegistroPeso $registro): void;

    /**
     * Identificador legible del observer (útil para logs y para
     * desuscribir un observer específico).
     */
    public function nombre(): string;
}
