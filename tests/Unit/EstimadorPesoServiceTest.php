<?php

declare(strict_types=1);

namespace BovWeight\Lab\Tests\Unit;

use BovWeight\Lab\Application\Services\EstimadorPesoService;
use BovWeight\Lab\Application\Strategies\AlgoritmoRegresionLineal;
use BovWeight\Lab\Application\Strategies\AlgoritmoTablaReferencia;
use BovWeight\Lab\Application\Strategies\AlgoritmoYolov8;
use BovWeight\Lab\Domain\Estimacion\ResultadoEstimacion;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class EstimadorPesoServiceTest extends TestCase
{
    private array $datos;

    protected function setUp(): void
    {
        $this->datos = [
            'pixeles_largo'      => 480,
            'perimetro_torax_cm' => 178,
            'longitud_cuerpo_cm' => 145,
            'edad_meses'         => 24,
            'factor_raza'        => 1.05,
        ];
    }

    public function testYolov8ConConexionDevuelveResultado(): void
    {
        $service = new EstimadorPesoService(new AlgoritmoYolov8(true));
        $resultado = $service->estimar($this->datos);

        $this->assertInstanceOf(ResultadoEstimacion::class, $resultado);
        $this->assertSame('yolov8', $resultado->metodoUsado);
        $this->assertGreaterThan(0, $resultado->pesoKg);
        $this->assertEqualsWithDelta(92.5, $resultado->confianzaPorcentaje, 0.001);
    }

    public function testYolov8SinConexionLanzaExcepcion(): void
    {
        $service = new EstimadorPesoService(new AlgoritmoYolov8(false));
        $this->expectException(RuntimeException::class);
        $service->estimar($this->datos);
    }

    public function testCambioDeEstrategiaEnTiempoDeEjecucion(): void
    {
        $service = new EstimadorPesoService(new AlgoritmoYolov8(true));
        $service->setAlgoritmo(new AlgoritmoRegresionLineal());

        $resultado = $service->estimar($this->datos);
        $this->assertSame('regresion_lineal', $resultado->metodoUsado);
    }

    public function testFallbackAutomaticoCuandoYolov8NoEstaDisponible(): void
    {
        $service = new EstimadorPesoService(new AlgoritmoYolov8(false));

        $resultado = $service->estimarConFallback(
            $this->datos,
            new AlgoritmoTablaReferencia()
        );

        $this->assertSame('tabla_referencia', $resultado->metodoUsado);
    }

    public function testResultadoEsInmutable(): void
    {
        $resultado = new ResultadoEstimacion(450.0, 90.0, 'yolov8');
        // PHP 8.1 readonly: intentar mutar lanza Error.
        $this->expectException(\Error::class);
        $resultado->pesoKg = 999.0;
    }
}
