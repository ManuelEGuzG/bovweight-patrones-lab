<?php

declare(strict_types=1);

require __DIR__ . '/../autoload.php';

use BovWeight\Lab\Application\Observers\AlertaSMS;
use BovWeight\Lab\Application\Observers\NotificadorPropietario;
use BovWeight\Lab\Application\Observers\RecalculadorICC;
use BovWeight\Lab\Application\Observers\WebhookSenasa;
use BovWeight\Lab\Domain\RegistroPeso\RegistroPeso;
use BovWeight\Lab\Domain\RegistroPeso\RegistroPesoSubject;
use BovWeight\Lab\Infrastructure\Container\ServiceContainer;
use BovWeight\Lab\Infrastructure\Container\ServiceProvider;

echo "============================================================\n";
echo "  DEMO PATRÓN OBSERVER - BovWeight CR\n";
echo "============================================================\n\n";

$c = new ServiceContainer();
ServiceProvider::register($c);

/** @var RegistroPesoSubject $subject */
$subject = $c->make(RegistroPesoSubject::class);

// Observadores iniciales (los 3 que pide la guía).
$notificador = new NotificadorPropietario();
$icc         = new RecalculadorICC();
$webhook     = new WebhookSenasa();

$subject->suscribir($notificador);
$subject->suscribir($icc);
$subject->suscribir($webhook);

echo "Observadores suscritos inicialmente:\n";
foreach ($subject->getObservadores() as $o) {
    echo "  - {$o->nombre()}\n";
}
echo "\n";

// Simulamos un primer pesaje (peso bajo).
$pesaje1 = new RegistroPeso(
    arete: 'CR-0001',
    pesoKg: 320.5,
    metodoUsado: 'yolov8',
    fecha: new DateTimeImmutable('2026-05-15 09:00')
);
echo "Notificando pesaje #1: arete=CR-0001, peso=320.5 kg\n";
$subject->notificar($pesaje1);
echo "\n";

// Agregamos AlertaSMS DESPUÉS, sin modificar nada del Subject.
echo ">>> AGREGAMOS AlertaSMS sin modificar Subject ni observadores existentes <<<\n\n";
$sms = new AlertaSMS();
$subject->suscribir($sms);

// Segundo pesaje (peso alto -> dispara SMS).
$pesaje2 = new RegistroPeso(
    arete: 'CR-0003',
    pesoKg: 525.0,
    metodoUsado: 'regresion_lineal',
    fecha: new DateTimeImmutable('2026-05-15 14:30')
);
echo "Notificando pesaje #2: arete=CR-0003, peso=525 kg\n";
$subject->notificar($pesaje2);
echo "\n";

// Estado final de los observadores.
echo "Estado final de los observadores:\n";
echo "  NotificadorPropietario - emails:\n";
foreach ($notificador->emailsEnviados as $msg) echo "      • {$msg}\n";

echo "  RecalculadorICC - ICCs calculados:\n";
foreach ($icc->iccCalculados as $arete => $valor) echo "      • {$arete} -> ICC {$valor}\n";

echo "  WebhookSenasa - payloads enviados:\n";
foreach ($webhook->payloadsEnviados as $p) {
    echo "      • " . json_encode($p, JSON_UNESCAPED_UNICODE) . "\n";
}

echo "  AlertaSMS - SMS enviados:\n";
foreach ($sms->smsEnviados as $s) echo "      • {$s}\n";

echo "\n";
echo "Observación clave:\n";
echo "  - El controlador que llama notificar() NO conoce a los observadores.\n";
echo "  - Añadir AlertaSMS NO modificó RegistroPesoSubject ni los observers previos.\n";
echo "  - Solo NotificadorPropietario reaccionó a ambos pesajes; AlertaSMS solo\n";
echo "    al segundo (peso >= 500 kg).\n";
