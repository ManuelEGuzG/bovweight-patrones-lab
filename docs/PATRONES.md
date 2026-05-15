# Análisis Pre-Código de Patrones — BovWeight CR

> Documento producido conforme al paso 1 de la guía:
> *"Lea la tarjeta de contexto del patrón [...] No salte al código antes de
> completar el análisis."*

---

## 1. Factory Method

### Problema observable en BovWeight CR
En el código actual existen al menos **6 puntos** distintos donde se crea una
raza con `new Brahman()` o `new Nelore()` (controladores de registro de
animal, importadores CSV, seeders, comandos artisan, formularios de
edición, vista del expediente animal). Cuando SENASA aprueba la raza
Angus, debemos cambiar los **6 archivos** y existe el riesgo de olvidar
alguno.

### Decisión
Aplicar Factory Method centralizando la creación en `RazaFactory`. El
mapeo se realiza con un **array asociativo** (no `switch`) porque:
- Permite registrar razas en runtime si fuera necesario.
- El método `create()` permanece **cerrado a modificación** y abierto a
  extensión (Open/Closed Principle).

### Participantes GoF (mapeo al proyecto)

| Rol GoF | Clase en este lab |
|---------|-------------------|
| Creator (interfaz) | `IRazaFactory` |
| ConcreteCreator | `RazaFactory` |
| Product (abstracción) | `Raza` |
| ConcreteProduct | `Brahman`, `Nelore`, `Angus` |

### Cómo se "refactorizan" los 2 puntos de creación
1. `EloquentAnimalRepository::hidratar()` — al reconstruir un Animal desde
   la BD, llama a `$this->razaFactory->create($fila['raza'])` en lugar de
   `new Brahman()`.
2. `examples/demo_strategy.php` — al preparar los datos de entrada para
   el estimador, obtiene `$brahman = $factory->create('brahman')`
   en lugar de instanciar directamente.

---

## 2. Repository

### Problema observable
`Animal::where('rancho_id', $id)->with('registrosPeso')->get()` se repite en
`ReporteService`, `EstimadorService` y `DashboardController`. Si el equipo
decide agregar **cache Redis**, debemos modificar 3 archivos. Si el equipo
migra de Eloquent a Doctrine, lo mismo.

### Decisión
Crear `IAnimalRepository` con vocabulario del **dominio**, no del ORM. La
fachada habla de aretes y ranchos, no de `where()` ni `with()`.

### Participantes (mapeo al proyecto)

| Rol | Clase |
|-----|-------|
| Repository Interface | `IAnimalRepository` |
| Concrete Repository (producción) | `EloquentAnimalRepository` |
| In-Memory Repository (tests) | `InMemoryAnimalRepository` |

### Beneficios concretos
- **Pruebas unitarias**: `ReporteServiceTest` corre con `InMemoryAnimalRepository`
  en milisegundos, sin migraciones de BD.
- **Migración futura**: cuando el equipo decida usar Doctrine, basta con
  cambiar el binding del `ServiceProvider`. Cero líneas del dominio cambian.
- **Cache**: introducir una capa `CachedAnimalRepository` que decora a
  `EloquentAnimalRepository` no obliga a tocar el dominio.

---

## 3. Observer

### Problema observable
El controlador `RegistroController::store()` actualmente ejecuta 4
acciones secuenciales tras guardar un peso:
1. Enviar email al propietario.
2. Actualizar el dashboard.
3. Recalcular ICC.
4. Disparar webhook a SENASA.

Cuando operaciones pide **AlertaSMS**, hay que abrir ese método y
agregar la quinta línea. El método ya tiene 80+ líneas y mezcla
responsabilidades.

### Decisión
Aplicar Observer GoF. `RegistroPesoSubject` mantiene la lista de
suscriptores; el controlador solo llama `notificar($registro)`. Los
observers concretos viven en archivos separados.

### Participantes (mapeo al proyecto)

| Rol GoF | Clase |
|---------|-------|
| Subject (Observable) | `RegistroPesoSubject` |
| Observer (interfaz) | `IRegistroPesoObserver` |
| ConcreteObservers | `NotificadorPropietario`, `RecalculadorICC`, `WebhookSenasa`, `AlertaSMS` |

### Demostración de Open/Closed
`AlertaSMS` se añadió **sin modificar**:
- `RegistroPesoSubject`
- `IRegistroPesoObserver`
- Los 3 observadores existentes
- El controlador

Solo se agregó una llamada `$subject->suscribir(new AlertaSMS())` en el
`ServiceProvider` (en Laravel real iría en `EventServiceProvider::boot()`).

### Nota Laravel
En la versión Laravel real, esto se implementa con `Events/Listeners`:
- `RegistroPesoSubject` ↔ `event(new PesoRegistrado($registro))`
- `IRegistroPesoObserver::onPesoRegistrado()` ↔ `Listener::handle()`
- Los listeners se registran en `EventServiceProvider::$listen`.

Hemos implementado la versión manual del patrón para que sea **explícito**
qué hace cada parte; en producción se usaría la facilidad de Laravel.

---

## 4. Strategy

### Problema observable
`EstimadorPesoService::estimar()` tiene aproximadamente este código:

```php
if ($metodo === 'yolov8') {
    // 30 líneas de orquestación HTTP, parsing, validación
} elseif ($metodo === 'regresion') {
    // 15 líneas de fórmula
} elseif ($metodo === 'tabla') {
    // 10 líneas de lookup
}
```

Agregar un nuevo algoritmo (red neuronal local TensorFlow Lite) exige
abrir esta clase, añadir un cuarto `elseif`, y manejar la nueva lógica
en medio de la orquestación del flujo. Las 4 lógicas terminan acopladas.

### Decisión
Aplicar Strategy. Cada algoritmo es su propia clase con una sola
responsabilidad. `EstimadorPesoService` delega a la estrategia inyectada
y desconoce cuál es.

### Participantes (mapeo al proyecto)

| Rol GoF | Clase |
|---------|-------|
| Strategy (interfaz) | `IAlgoritmoEstimacion` |
| ConcreteStrategies | `AlgoritmoYolov8`, `AlgoritmoRegresionLineal`, `AlgoritmoTablaReferencia` |
| Context | `EstimadorPesoService` |
| Value Object resultado | `ResultadoEstimacion` (readonly) |

### Cambio en tiempo de ejecución
`EstimadorPesoService::estimarConFallback()` es el caso clásico
mencionado por el cliente Don Alonso Chavarría: *"Cuando uno está en el
campo a veces no tiene internet"*. Cuando YOLOv8 reporta
`estaDisponible() === false`, el contexto cambia automáticamente a la
Tabla de Referencia sin que el código cliente lo note.

### Inmutabilidad del Value Object
`ResultadoEstimacion` usa propiedades `readonly` de PHP 8.1. Esto
garantiza que ningún consumidor (controlador, vista, exportador PDF
para Don Diego Chavarría el comprador) pueda alterar accidentalmente
los valores devueltos por el algoritmo.

---

## Resumen del impacto

Antes de los patrones, agregar Angus + AlertaSMS + un nuevo algoritmo
requería modificar **al menos 10 archivos** existentes. Después de los
patrones, agregar lo mismo requiere:

| Cambio | Archivos modificados | Archivos nuevos |
|--------|---------------------|-----------------|
| Raza Angus | 1 línea en `RazaFactory` | 1 (`Angus.php`) |
| AlertaSMS | 1 línea en `ServiceProvider` | 1 (`AlertaSMS.php`) |
| Algoritmo nuevo | 1 línea en `ServiceProvider` | 1 (clase del algoritmo) |

Esa es la ganancia tangible que justifica el costo inicial de
introducir las cuatro abstracciones.
