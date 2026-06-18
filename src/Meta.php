<?php

declare(strict_types=1);

namespace InfCompany\ApiResponseBase;

class Meta
{
    public function __construct(
        private readonly ?array $abilities = null,
        private readonly ?array $pagination = null,
        private readonly ?array $additional = null,
        private readonly ?string $requestId = null,
    ) {
    }

    public static function abilities(array $abilities): self
    {
        return new self(abilities: $abilities);
    }

    public static function pagination(int $total, int $perPage, int $currentPage): self
    {
        return new self(pagination: [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $currentPage,
        ]);
    }

    public static function fromPaginator(object $paginator): self
    {
        return new self(pagination: [
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
        ]);
    }

    public static function additional(array $additional): self
    {
        return new self(additional: $additional);
    }

    public static function requestId(string $id): self
    {
        return new self(requestId: $id);
    }

    public function withAbilities(array $abilities): self
    {
        return new self($abilities, $this->pagination, $this->additional, $this->requestId);
    }

    public function withPagination(array $pagination): self
    {
        return new self($this->abilities, $pagination, $this->additional, $this->requestId);
    }

    public function withAdditional(array $additional): self
    {
        return new self($this->abilities, $this->pagination, $additional, $this->requestId);
    }

    public function withRequestId(string $id): self
    {
        return new self($this->abilities, $this->pagination, $this->additional, $id);
    }

    public function merge(self $other): self
    {
        return new self(
            abilities: $other->abilities ?? $this->abilities,
            pagination: $other->pagination ?? $this->pagination,
            additional: $other->additional ?? $this->additional,
            requestId: $other->requestId ?? $this->requestId,
        );
    }

    public function isEmpty(): bool
    {
        return $this->abilities === null
            && $this->pagination === null
            && $this->additional === null
            && $this->requestId === null;
    }

    public function getAbilities(): ?array
    {
        return $this->abilities;
    }

    public function getPagination(): ?array
    {
        return $this->pagination;
    }

    public function getAdditional(): ?array
    {
        return $this->additional;
    }

    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    public function toArray(): array
    {
        $meta = [];

        if ($this->abilities !== null) {
            $meta['abilities'] = $this->abilities;
        }

        if ($this->pagination !== null) {
            $meta['pagination'] = $this->pagination;
        }

        if ($this->requestId !== null) {
            $meta['request_id'] = $this->requestId;
        }

        return $meta;
    }
}
