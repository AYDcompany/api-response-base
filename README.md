# API Response Base

Framework-agnostic JSON API response builder. Provides a consistent response envelope with support for pagination metadata, abilities, and request tracing.

## Installation

```bash
composer require inf-company/api-response-base
```

## Response Envelope

Every response follows this structure:

```json
{
    "code": 200,
    "data": {},
    "message": "Successful",
    "meta": {
        "abilities": { "create": true, "delete": false },
        "pagination": { "total": 100, "per_page": 20, "current_page": 1 },
        "request_id": "550e8400-e29b-41d4-a716-446655440000"
    }
}
```

The `meta` key is omitted when empty. `data` defaults to `[]` when null. A `request_id` (UUID v4) is always auto-generated.

## Usage

### Building Responses

All methods are static and return arrays:

```php
use InfCompany\ApiResponseBase\Response;
use InfCompany\ApiResponseBase\StatusCode;

// Using convenience methods
$response = Response::ok($data);
$response = Response::created($data, 'Resource created');
$response = Response::noContent();

// Error responses
$response = Response::badRequest('Invalid input', $errors);
$response = Response::unauthorized();
$response = Response::forbidden();
$response = Response::notFound('User not found');
$response = Response::unprocessable('Validation failed', $errors);
$response = Response::internalError();

// Custom status code
$response = Response::build(StatusCode::CREATED, $data, 'Created');
$response = Response::build(418, null, "I'm a teapot");
```

### Using `Meta`

`Meta` is an immutable value object. All setters return new instances:

```php
use InfCompany\ApiResponseBase\Meta;

// Create with static factories
$meta = Meta::requestId('custom-id');
$meta = Meta::abilities(['create' => true, 'delete' => false]);
$meta = Meta::pagination(total: 100, perPage: 20, currentPage: 1);

// Immutable setters
$meta = $meta->withAbilities(['edit' => true]);
$meta = $meta->withRequestId('another-id');

// Merge two Meta objects (the other wins on conflict)
$merged = $paginationMeta->merge($abilitiesMeta);
```

### Paginator Support

`Response::build()` automatically detects paginator-like objects via duck typing. Any object implementing `total()`, `perPage()`, `currentPage()`, and `items()` will have its pagination extracted into `meta` and `data` replaced with the items array:

```php
// Works with both Laravel's and Hyperf's paginator
$response = Response::ok($paginator);
```

You can also build pagination metadata manually:

```php
$meta = Meta::fromPaginator($paginator); // duck-typed
$meta = Meta::pagination(total: 50, perPage: 10, currentPage: 2);
```

### Abilities Resolver

Implement `AbilitiesResolver` to inject authorization flags into every response:

```php
use InfCompany\ApiResponseBase\Contracts\AbilitiesResolver;

class MyAbilitiesResolver implements AbilitiesResolver
{
    public function resolve(mixed $user = null): array
    {
        return [
            'create' => $user?->can('create') ?? false,
            'delete' => $user?->can('delete') ?? false,
        ];
    }
}
```

### `BuildsApiResponse` Trait

Provides shared logic for framework-specific implementations:

```php
use InfCompany\ApiResponseBase\Concerns\BuildsApiResponse;

class MyService
{
    use BuildsApiResponse;

    public function handle()
    {
        $meta = $this->buildMeta();        // auto-generates request_id, auto-resolves abilities
        $meta = $this->resolveAbilitiesMeta($user); // resolve for a specific user
    }
}
```

## StatusCode Enum

```php
use InfCompany\ApiResponseBase\StatusCode;

StatusCode::OK;                 // 200
StatusCode::CREATED;            // 201
StatusCode::ACCEPTED;           // 202
StatusCode::NO_CONTENT;         // 204
StatusCode::BAD_REQUEST;        // 400
StatusCode::UNAUTHORIZED;       // 401
StatusCode::FORBIDDEN;          // 403
StatusCode::NOT_FOUND;          // 404
StatusCode::UNPROCESSABLE_ENTITY; // 422
StatusCode::INTERNAL_SERVER_ERROR; // 500
```

## Requirements

- PHP ^8.1
- ramsey/uuid ^4.9

## License

MIT
