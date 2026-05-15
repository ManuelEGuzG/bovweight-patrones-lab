<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Raza;

use InvalidArgumentException;


final class RazaFactory implements IRazaFactory
{
    
    private array $mapaRazas;

    public function __construct()
    {
        $this->mapaRazas = [
            'brahman' => fn (): Raza => new Brahman(),
            'nelore'  => fn (): Raza => new Nelore(),
            'angus'   => fn (): Raza => new Angus(),
        ];
    }

    public function create(string $nombreRaza): Raza
    {
        $clave = strtolower(trim($nombreRaza));

        if (!isset($this->mapaRazas[$clave])) {
            throw new InvalidArgumentException(
                "Raza '{$nombreRaza}' no está registrada en el catálogo. " .
                "Razas disponibles: " . implode(', ', $this->razasDisponibles())
            );
        }

        return ($this->mapaRazas[$clave])();
    }

    public function razasDisponibles(): array
    {
        return array_keys($this->mapaRazas);
    }
}
