<?php

declare(strict_types=1);

namespace Ayd\ApiResponseBase\Concerns;

use Ayd\ApiResponseBase\Meta;
use Ayd\ApiResponseBase\Contracts\AbilitiesResolver;

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

        if (
            $this->abilitiesResolver !== null &&
            $meta->getAbilities() === null
        ) {
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
