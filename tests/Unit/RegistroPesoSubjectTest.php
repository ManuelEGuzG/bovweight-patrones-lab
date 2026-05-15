<?php

declare(strict_types=1);

namespace BovWeight\Lab\Tests\Unit;

use BovWeight\Lab\Domain\RegistroPeso\IRegistroPesoObserver;
use BovWeight\Lab\Domain\RegistroPeso\RegistroPeso;
use BovWeight\Lab\Domain\RegistroPeso\RegistroPesoSubject;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class RegistroPesoSubjectTest extends TestCase
{
    public function testNotificarLlamaATodosLosObservadoresSuscritos(): void
    {
        $subject = new RegistroPesoSubject();
        $registro = new RegistroPeso(
            'CR-0001',
            450.0,
            'yolov8',
            new DateTimeImmutable('2026-05-15')
        );

        $obs1 = $this->createMock(IRegistroPesoObserver::class);
        $obs2 = $this->createMock(IRegistroPesoObserver::class);
        $obs3 = $this->createMock(IRegistroPesoObserver::class);

        $obs1->expects($this->once())->method('onPesoRegistrado')->with($registro);
        $obs2->expects($this->once())->method('onPesoRegistrado')->with($registro);
        $obs3->expects($this->once())->method('onPesoRegistrado')->with($registro);

        $subject->suscribir($obs1);
        $subject->suscribir($obs2);
        $subject->suscribir($obs3);

        $subject->notificar($registro);
    }

    public function testNoSuscribirDuplicadosPorInstancia(): void
    {
        $subject = new RegistroPesoSubject();
        $obs = $this->createMock(IRegistroPesoObserver::class);

        $subject->suscribir($obs);
        $subject->suscribir($obs); // duplicado

        $this->assertCount(1, $subject->getObservadores());
    }

    public function testDesuscribirDetieneLasNotificaciones(): void
    {
        $subject = new RegistroPesoSubject();
        $registro = new RegistroPeso(
            'CR-0002',
            300.0,
            'tabla',
            new DateTimeImmutable()
        );

        $obs = $this->createMock(IRegistroPesoObserver::class);
        $obs->expects($this->never())->method('onPesoRegistrado');

        $subject->suscribir($obs);
        $subject->desuscribir($obs);
        $subject->notificar($registro);
    }
}
