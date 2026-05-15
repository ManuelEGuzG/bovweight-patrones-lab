<?php

declare(strict_types=1);

namespace BovWeight\Lab\Domain\Estimacion;

/**
 * Strategy (interfaz) del patrón Strategy GoF.
 *
 * Cada algoritmo de estimación de peso encapsulado en su propia clase
 * implementa esta interfaz. El contexto (EstimadorPesoService) los usa
 * intercambiablemente sin if-else.
 */
interface IAlgoritmoEstimacion
{
    /**
     * Ejecuta el algoritmo de estimación con los datos de entrada
     * (foto, medidas, raza, etc.) y devuelve el resultado inmutable.
     *
     * @param array<string, mixed> $datosEntrada
     */
    public function ejecutar(array $datosEntrada): ResultadoEstimacion;

    /**
     * Indica si el algoritmo puede operar en las condiciones actuales.
     * Ejemplo: YOLOv8 requiere internet, así que devolverá false en
     * potreros sin señal (caso mencionado por Don Alonso Chavarría).
     */
    public function estaDisponible(): bool;
}
