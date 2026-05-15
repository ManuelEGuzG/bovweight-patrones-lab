<?php

declare(strict_types=1);

namespace BovWeight\Lab\Tests\Unit;

use BovWeight\Lab\Application\Services\ReporteService;
use BovWeight\Lab\Domain\Animal\Animal;
use BovWeight\Lab\Domain\Raza\RazaFactory;
use BovWeight\Lab\Infrastructure\Persistence\InMemoryAnimalRepository;
use PHPUnit\Framework\TestCase;

final class ReporteServiceTest extends TestCase
{
    public function testReporteUsaRepositorioEnMemoriaSinTocarBD(): void
    {
        $repo = new InMemoryAnimalRepository();
        $factory = new RazaFactory();

        $repo->save(new Animal('CR-001', $factory->create('brahman'), 1, 'Lucero'));
        $repo->save(new Animal('CR-002', $factory->create('nelore'),  1, null));
        $repo->save(new Animal('CR-003', $factory->create('angus'),   2, 'Tornado'));

        $reporte = new ReporteService($repo);
        $filas = $reporte->reporteAnimalesPorRancho(1);

        $this->assertCount(2, $filas);
        $this->assertSame('CR-001', $filas[0]['arete']);
        $this->assertSame('Brahman', $filas[0]['raza']);
        $this->assertSame('Lucero', $filas[0]['nombre']);
        $this->assertNull($filas[1]['nombre']);
    }

    public function testRanchoSinAnimalesDevuelveReporteVacio(): void
    {
        $repo = new InMemoryAnimalRepository();
        $reporte = new ReporteService($repo);

        $this->assertSame([], $reporte->reporteAnimalesPorRancho(999));
    }

    public function testRepositorioEnMemoriaSoporteCRUD(): void
    {
        $repo = new InMemoryAnimalRepository();
        $factory = new RazaFactory();

        $repo->save(new Animal('CR-100', $factory->create('brahman'), 5));
        $this->assertSame(1, $repo->count());

        $encontrado = $repo->findByArete('CR-100');
        $this->assertNotNull($encontrado);
        $this->assertSame('CR-100', $encontrado->getArete());

        $repo->delete('CR-100');
        $this->assertNull($repo->findByArete('CR-100'));
        $this->assertSame(0, $repo->count());
    }
}
