<?php

declare(strict_types=1);

namespace BovWeight\Lab\Application\Observers;

use BovWeight\Lab\Domain\RegistroPeso\IRegistroPesoObserver;
use BovWeight\Lab\Domain\RegistroPeso\RegistroPeso;

/**
 * ConcreteObserver: recalcula el ICC (Indicador de Condición Corporal)
 * del animal tras cada nuevo pesaje.
 *
 * El Dr. Caleb Chavarría (veterinario) mencionó en la entrevista que es útil
 * saber si una novilla está ganando peso como debería; el ICC ayuda a eso.
 */
final class RecalculadorICC implements IRegistroPesoObserver
{
    /** @var array<string, float> */
    public array $iccCalculados = [];

    public function onPesoRegistrado(RegistroPeso $registro): void
    {
        // Cálculo simplificado del ICC para fines del laboratorio.
        // En producción se usaría el historial completo del animal.
        $icc = min(5.0, max(1.0, $registro->getPesoKg() / 100.0));
        $this->iccCalculados[$registro->getArete()] = round($icc, 2);
    }

    public function nombre(): string
    {
        return 'RecalculadorICC';
    }
}
