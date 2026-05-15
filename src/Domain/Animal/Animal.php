<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Animal;

use BovWeight\Lab\Domain\Raza\Raza;
use BovWeight\Lab\Domain\RegistroPeso\RegistroPeso;

/**
 * Entidad de dominio Animal.
 *
 * Identificada por el número de arete que pide SENASA, según lo expresado
 * por Don Iván Chavarría en la entrevista de levantamiento.
 */
final class Animal
{
    /** @var RegistroPeso[] */
    private array $registrosPeso = [];

    public function __construct(
        private readonly string $arete,
        private readonly Raza $raza,
        private readonly int $ranchoId,
        private ?string $nombre = null
    ) {}

    public function getArete(): string
    {
        return $this->arete;
    }

    public function getRaza(): Raza
    {
        return $this->raza;
    }

    public function getRanchoId(): int
    {
        return $this->ranchoId;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function agregarRegistroPeso(RegistroPeso $registro): void
    {
        $this->registrosPeso[] = $registro;
    }

    /** @return RegistroPeso[] */
    public function getRegistrosPeso(): array
    {
        return $this->registrosPeso;
    }
}
