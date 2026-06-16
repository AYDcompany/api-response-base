<?php

declare(strict_types=1);

namespace InfCompany\ApiResponseBase\Contracts;

interface AbilitiesResolver
{
    /**
     * @return array<string, bool>
     */
    public function resolve(mixed $user = null): array;
}
