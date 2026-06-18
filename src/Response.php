<?php

declare(strict_types=1);

namespace InfCompany\ApiResponseBase;

class Response
{
    public static function build(
        bool $success,
        int|StatusCode $code,
        mixed $data = null,
        string $message = "Successful",
        ?Meta $meta = null,
    ): array {
        $httpCode = $code instanceof StatusCode ? $code->value : $code;

        // Auto-extract pagination from paginator-like objects
        if (self::isPaginator($data)) {
            $paginationMeta = Meta::fromPaginator($data);
            $data = $data->items();
            $meta = $meta ? $paginationMeta->merge($meta) : $paginationMeta;
        }

        $payload = [
            "code" => $httpCode,
            "data" => self::normalizeData($data),
            "success" => $success,
            "message" => $message,
        ];

        if ($meta && !$meta->isEmpty()) {
            $payload["meta"] = $meta->toArray();
        }

        return $payload;
    }

    public static function ok(
        mixed $data = null,
        string $message = "Successful",
        ?Meta $meta = null,
    ): array {
        return self::build(StatusCode::OK, $data, $message, $meta);
    }

    public static function created(
        mixed $data = null,
        string $message = "Created",
        ?Meta $meta = null,
    ): array {
        return self::build(StatusCode::CREATED, $data, $message, $meta);
    }

    public static function accepted(
        mixed $data = null,
        string $message = "Accepted",
        ?Meta $meta = null,
    ): array {
        return self::build(StatusCode::ACCEPTED, $data, $message, $meta);
    }

    public static function noContent(): array
    {
        return self::build(StatusCode::NO_CONTENT, null, "No Content");
    }

    public static function badRequest(
        string $message = "Bad Request",
        mixed $data = null,
        ?Meta $meta = null,
    ): array {
        return self::build(StatusCode::BAD_REQUEST, $data, $message, $meta);
    }

    public static function unauthorized(
        string $message = "Unauthorized",
        ?Meta $meta = null,
    ): array {
        return self::build(StatusCode::UNAUTHORIZED, null, $message, $meta);
    }

    public static function forbidden(
        string $message = "Forbidden",
        ?Meta $meta = null,
    ): array {
        return self::build(StatusCode::FORBIDDEN, null, $message, $meta);
    }

    public static function notFound(
        string $message = "Not Found",
        ?Meta $meta = null,
    ): array {
        return self::build(StatusCode::NOT_FOUND, null, $message, $meta);
    }

    public static function unprocessable(
        string $message = "Unprocessable Entity",
        mixed $data = null,
        ?Meta $meta = null,
    ): array {
        return self::build(
            StatusCode::UNPROCESSABLE_ENTITY,
            $data,
            $message,
            $meta,
        );
    }

    public static function internalError(
        string $message = "Internal Server Error",
        ?Meta $meta = null,
    ): array {
        return self::build(
            StatusCode::INTERNAL_SERVER_ERROR,
            null,
            $message,
            $meta,
        );
    }

    protected static function normalizeData(mixed $data): mixed
    {
        if ($data === null) {
            return new \stdClass();
        }

        return $data;
    }

    private static function isPaginator(mixed $data): bool
    {
        if (!is_object($data)) {
            return false;
        }

        // When using an API Resource to wrap a paginator
        if (property_exists($data, 'resource')) {
            $data = $data->resource;
        }

        return method_exists($data, "total") &&
            method_exists($data, "perPage") &&
            method_exists($data, "currentPage") &&
            method_exists($data, "items");
    }
}
