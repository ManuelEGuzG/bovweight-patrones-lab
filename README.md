# Laboratorio: Catálogo de Patrones en BovWeight CR

**Curso:** IF7100 — Ingeniería del Software I
**Sede:** UCR — Sede de Guanacaste, Recinto de Liberia
**Docente:** Lic. Alonso Chavarría Cubero
**Estudiantes:** Naillel David Bermudez Romero & Manuel Esteban Guzman Gomez
**Tipo:** Rama alterna de práctica (independiente del proyecto principal)

Este repositorio contiene la implementación de los cuatro patrones de diseño
solicitados en el laboratorio, aplicados al dominio del sistema **BovWeight CR**
(estimación de peso de ganado bovino mediante análisis de fotografías).

---

## Patrones implementados

| # | Patrón | Tipo (GoF) | Caso aplicado |
|---|--------|-----------|---------------|
| 1 | **Factory Method** | Creacional | `RazaFactory` produce `Brahman`, `Nelore`, `Angus` |
| 2 | **Repository** | Fowler / DDD | `IAnimalRepository` desacopla negocio de Eloquent |
| 3 | **Observer** | Comportamiento | `RegistroPesoSubject` notifica a 4 observadores |
| 4 | **Strategy** | Comportamiento | `EstimadorPesoService` intercambia 3 algoritmos |

---

## Estructura del proyecto

```
bovweight-patrones-lab/
├── composer.json
├── phpunit.xml
├── autoload.php                 # Autoloader manual (para correr sin composer install)
├── README.md
│
├── src/
│   ├── Domain/
│   │   ├── Raza/                # Factory Method
│   │   │   ├── Raza.php                 (Product abstracto)
│   │   │   ├── Brahman.php              (ConcreteProduct)
│   │   │   ├── Nelore.php               (ConcreteProduct)
│   │   │   ├── Angus.php                (ConcreteProduct - agregado sin tocar factory)
│   │   │   ├── IRazaFactory.php         (Creator)
│   │   │   └── RazaFactory.php          (ConcreteCreator)
│   │   │
│   │   ├── Animal/              # Repository
│   │   │   ├── Animal.php               (Entidad)
│   │   │   └── IAnimalRepository.php    (Repository interface)
│   │   │
│   │   ├── RegistroPeso/        # Observer
│   │   │   ├── RegistroPeso.php         (Entidad)
│   │   │   ├── IRegistroPesoObserver.php  (Observer interface)
│   │   │   └── RegistroPesoSubject.php  (Subject / Observable)
│   │   │
│   │   └── Estimacion/          # Strategy
│   │       ├── IAlgoritmoEstimacion.php (Strategy interface)
│   │       └── ResultadoEstimacion.php  (Value Object readonly)
│   │
│   ├── Application/
│   │   ├── Services/
│   │   │   ├── ReporteService.php       (usa Repository)
│   │   │   └── EstimadorPesoService.php (Context del Strategy)
│   │   ├── Observers/
│   │   │   ├── NotificadorPropietario.php
│   │   │   ├── RecalculadorICC.php
│   │   │   ├── WebhookSenasa.php
│   │   │   └── AlertaSMS.php            (agregado sin modificar Subject)
│   │   └── Strategies/
│   │       ├── AlgoritmoYolov8.php
│   │       ├── AlgoritmoRegresionLineal.php
│   │       └── AlgoritmoTablaReferencia.php
│   │
│   └── Infrastructure/
│       ├── Persistence/
│       │   ├── EloquentAnimalRepository.php
│       │   └── InMemoryAnimalRepository.php
│       └── Container/
│           ├── ServiceContainer.php
│           └── ServiceProvider.php
│
├── examples/                    # Demos ejecutables (uno por patrón)
│   ├── demo_factory.php
│   ├── demo_repository.php
│   ├── demo_observer.php
│   └── demo_strategy.php
│
├── tests/Unit/                  # Pruebas PHPUnit
│   ├── RazaFactoryTest.php
│   ├── ReporteServiceTest.php
│   ├── RegistroPesoSubjectTest.php
│   └── EstimadorPesoServiceTest.php
│
└── docs/
    └── PATRONES.md              # Análisis pre-código por patrón
```

---

## Requisitos

- PHP **8.1** o superior (se usan `readonly properties` y `enums`)
- (Opcional) Composer para correr PHPUnit

Verifica tu versión:

```bash
php -v
```

---

## Ejecutar las demos (sin Composer)

Cada patrón tiene su demo independiente. Desde la raíz del proyecto:

```bash
php examples/demo_factory.php
php examples/demo_repository.php
php examples/demo_observer.php
php examples/demo_strategy.php
```

Cada script:
1. Construye el contenedor de servicios.
2. Resuelve dependencias por inyección.
3. Ejecuta los escenarios pedidos por la guía del laboratorio.
4. Imprime resultados verificables.

---

## Ejecutar las pruebas unitarias

Con Composer:

```bash
composer install
composer test
```

O directamente con PHPUnit instalado globalmente:

```bash
phpunit --colors=always
```

Cobertura mínima esperada del laboratorio:

- `RazaFactoryTest` — 5 tests
- `ReporteServiceTest` — 3 tests (usando `InMemoryAnimalRepository`)
- `RegistroPesoSubjectTest` — 3 tests (mocks de observadores)
- `EstimadorPesoServiceTest` — 5 tests (incluyendo fallback)

---

## Cómo se cumplen los entregables del laboratorio

### Factory
- [x] Interfaz `IRazaFactory` con `create(string $nombreRaza): Raza`
- [x] `RazaFactory` usa **array asociativo**, no switch
- [x] Registrado como **singleton** en el `ServiceContainer`
- [x] Demo refactoriza ≥ 2 puntos de creación (ver `demo_repository.php` y `demo_strategy.php`,
      ambos usan el factory en lugar de `new Brahman()` directo)

### Repository
- [x] Interfaz `IAnimalRepository` con `findByArete`, `findAllByRancho`, `save`, `delete`
- [x] `EloquentAnimalRepository` simulando Eloquent
- [x] `InMemoryAnimalRepository` para tests
- [x] Binding en `ServiceProvider::register()` (equivalente a `$this->app->bind(...)`)
- [x] `ReporteService` recibe la interfaz por constructor injection

### Observer
- [x] Interfaz `IRegistroPesoObserver` con `onPesoRegistrado(RegistroPeso): void`
- [x] `RegistroPesoSubject` con `suscribir`, `desuscribir`, `notificar`
- [x] Tres observadores concretos: `NotificadorPropietario`, `RecalculadorICC`, `WebhookSenasa`
- [x] `AlertaSMS` agregado sin modificar Subject ni otros observers (demostrado en demo)
- [x] Test con mocks verifica que `notificar()` invoca a todos los suscriptores

### Strategy
- [x] Interfaz `IAlgoritmoEstimacion` con `ejecutar(array): ResultadoEstimacion`
- [x] Value Object `ResultadoEstimacion` con propiedades **readonly**
- [x] Tres estrategias concretas (YOLOv8, Regresión Lineal, Tabla de Referencia)
- [x] `EstimadorPesoService` recibe la estrategia por constructor injection
- [x] Método `estimar()` SIN if-else por algoritmo
- [x] Cambio de estrategia en tiempo de ejecución demostrado (fallback YOLOv8 → Tabla)

---

## Decisiones de diseño explicadas

Ver `docs/PATRONES.md` para el análisis previo de cada patrón:
qué problema resuelve, participantes GoF, justificación de elección.
