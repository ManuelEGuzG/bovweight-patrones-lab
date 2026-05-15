<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\RegistroPeso;

/**
 * Subject / Observable del patrón Observer GoF.
 *
 * Mantiene la lista de observadores y los notifica al guardar un peso.
 * IMPORTANTE: este sujeto NO conoce a sus observadores concretos.
 * Trabaja exclusivamente contra la interfaz IRegistroPesoObserver.
 *
 * Eso permite que agregar un cuarto observador (por ejemplo, AlertaSMS
 * solicitado por el equipo de operaciones) NO requiera modificar esta clase.
 */
final class RegistroPesoSubject
{
    /** @var IRegistroPesoObserver[] */
    private array $observadores = [];

    public function suscribir(IRegistroPesoObserver $observer): void
    {
        // Evitar duplicados por instancia.
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

    /**
     * Notifica a todos los observadores suscritos.
     * Visibilidad: privada en el laboratorio original; aquí la dejamos
     * pública para que el servicio aplicativo pueda dispararla.
     * En Laravel real, esto se invocaría desde el modelo Eloquent en
     * el hook `created` (Events/Listeners).
     */
    public function notificar(RegistroPeso $registro): void
    {
        foreach ($this->observadores as $observer) {
            $observer->onPesoRegistrado($registro);
        }
    }
}
