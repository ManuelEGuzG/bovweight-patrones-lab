<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Raza;

/**
 * ConcreteProduct: Raza Brahman.
 *
 * Raza dominante en la región Chorotega de Costa Rica.
 * Mencionada por Don Iván Chavarría (Finca La Esperanza, Liberia).
 */
final class Brahman extends Raza
{
    public function __construct()
    {
        parent::__construct('Brahman', 'India / Cebú');
    }

    public function factorAjustePeso(): float
    {
        // Brahman: conformación robusta, ajuste +5% sobre estimación base
        return 1.05;
    }

    public function aprobadaPorSenasa(): bool
    {
        return true;
    }
}
