<?php

declare(strict_types=1);

namespace InfCompany\ApiResponseBase\Concerns;

use InfCompany\ApiResponseBase\Meta;
use Ramsey\Uuid\Uuid;
use InfCompany\ApiResponseBase\Contracts\AbilitiesResolver;

trait BuildsApiResponse
{
    protected ?AbilitiesResolver $abilitiesResolver = null;

    public function setAbilitiesResolver(AbilitiesResolver $resolver): void
    {
        $this->abilitiesResolver = $resolver;
    }

    protected function buildMeta(?Meta $meta = null): Meta
    {
        if ($meta === null) {
            $meta = new Meta();
        }

        if ($meta->getRequestId() === null) {
            $meta = $meta->withRequestId(Uuid::uuid4()->toString());
        }

        if ($this->abilitiesResolver !== null && $meta->getAbilities() === null) {
            $meta = $meta->withAbilities($this->abilitiesResolver->resolve());
        }

        return $meta;
    }

    protected function resolveAbilitiesMeta(mixed $user = null): Meta
    {
        if ($this->abilitiesResolver === null) {
            return new Meta();
        }

        return Meta::abilities($this->abilitiesResolver->resolve($user));
    }
}
