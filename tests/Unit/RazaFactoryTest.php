<?php

declare(strict_types=1);

namespace BovWeight\Lab\Tests\Unit;

use BovWeight\Lab\Domain\Raza\Angus;
use BovWeight\Lab\Domain\Raza\Brahman;
use BovWeight\Lab\Domain\Raza\Nelore;
use BovWeight\Lab\Domain\Raza\RazaFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RazaFactoryTest extends TestCase
{
    private RazaFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new RazaFactory();
    }

    public function testCreaBrahman(): void
    {
        $raza = $this->factory->create('Brahman');
        $this->assertInstanceOf(Brahman::class, $raza);
        $this->assertSame('Brahman', $raza->getNombre());
        $this->assertEqualsWithDelta(1.05, $raza->factorAjustePeso(), 0.0001);
    }

    public function testCreaNeloreIgnorandoMayusculas(): void
    {
        $raza = $this->factory->create('NELORE');
        $this->assertInstanceOf(Nelore::class, $raza);
    }

    public function testCreaAngusSinModificarFactory(): void
    {
        // Demuestra Open/Closed: Angus se agregó sin tocar create().
        $raza = $this->factory->create('angus');
        $this->assertInstanceOf(Angus::class, $raza);
    }

    public function testRazaDesconocidaLanzaExcepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory->create('Holstein');
    }

    public function testListaRazasDisponibles(): void
    {
        $disponibles = $this->factory->razasDisponibles();
        $this->assertEqualsCanonicalizing(
            ['brahman', 'nelore', 'angus'],
            $disponibles
        );
    }
}
