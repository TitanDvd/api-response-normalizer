# MMT Api Response Normalizer

Trait para respuestas JSON normalizadas en APIs Laravel. Unifica el formato de éxito (`success`, `data`, `meta`) y de error (`success`, `message`, `error`) en todos los endpoints.

## Requisitos

- PHP ^8.2
- Laravel ^11.0 o ^12.0
- Illuminate HTTP & Support ^11.0|^12.0

## Instalación

```bash
composer require mmt/api-response-normalizer
```

Si usas [mmt/laravel-feature-scaffold](https://github.com/TitanDvd/laravel-feature-scaffold), este paquete se instala como dependencia y los controladores generados ya usan el trait.

## Uso

Usa el trait `ApiResponse` en controladores (o cualquier clase que devuelva respuestas HTTP):

```php
<?php

namespace App\Http\Controllers;

use MMT\ApiResponseNormalizer\ApiResponse;

class UserController
{
    use ApiResponse;

    public function index()
    {
        $users = User::all();
        return $this->success(['users' => $users], 'Listado de usuarios');
    }

    public function store(Request $request)
    {
        $user = User::create($request->validated());
        return $this->created($user->toArray(), 'Usuario creado');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return $this->noContent();
    }
}
```

## API del trait

### Respuestas de éxito

| Método | Código | Descripción |
|--------|--------|-------------|
| `success($data, $message, $meta, $code, $paginator, $filters)` | 200 (por defecto) | Respuesta genérica con `data` y `meta` (opcional paginación y filtros). |
| `created($data, $message)` | 201 | Recurso creado. |
| `accepted($data, $message)` | 202 | Petición aceptada (procesamiento asíncrono, etc.). |
| `noContent()` | 204 | Sin cuerpo (p. ej. tras un DELETE). |

### Respuestas de error

| Método | Código | Descripción |
|--------|--------|-------------|
| `error($message, $errorCode, $details, $status)` | 400 (por defecto) | Error genérico con código y detalles. |
| `unauthorized($message)` | 401 | No autorizado. |
| `validationError($errors, $message)` | 422 | Errores de validación (detalles en `error.details`). |

### Parámetros de `success()`

- **data** (array): Payload principal.
- **message** (string): Mensaje en `meta.message` (si no es vacío).
- **meta** (array): Metadatos adicionales.
- **code** (int): Código HTTP (por defecto 200).
- **paginator** (LengthAwarePaginator|null): Si se pasa, se añade `meta.pagination` y opcionalmente `meta.filters`.
- **filters** (array): Se incluye en `meta.filters` solo si hay paginador.

### Ejemplo con paginación

```php
$paginator = User::paginate(15);
$items = UserResource::collection($paginator->items());
return $this->success(
    ['users' => $items],
    'Listado paginado',
    [],
    200,
    $paginator,
    $request->only(['search', 'role'])
);
```

### Formato de respuesta

**Éxito:**

```json
{
  "success": true,
  "data": { ... },
  "meta": {
    "message": "Success",
    "pagination": { "current_page": 1, "per_page": 15, "total": 42, "last_page": 3 },
    "filters": { "search": "", "role": "admin" }
  }
}
```

**Error:**

```json
{
  "success": false,
  "message": "Validation Error",
  "error": {
    "code": "VALIDATION_ERROR",
    "details": { "email": ["The email field is required."] }
  }
}
```

## Licencia

MIT.
