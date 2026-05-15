<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Raza;

use InvalidArgumentException;

/**
 * ConcreteCreator del patrón Factory Method.
 *
 * IMPORTANTE: la guía del laboratorio exige mapeo por array asociativo,
 * NO por switch/if-else. Esto facilita agregar una raza nueva sin
 * modificar el cuerpo del método create().
 *
 * Para añadir Angus solamente se agregó una línea en $mapaRazas.
 * El método create() permaneció intacto (Open/Closed Principle).
 */
final class RazaFactory implements IRazaFactory
{
    /**
     * Mapa nombre -> closure que produce la instancia.
     * Usamos closures (no nombres de clase como string) para permitir
     * inyección de dependencias en futuras razas que la requieran.
     *
     * @var array<string, \Closure>
     */
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
