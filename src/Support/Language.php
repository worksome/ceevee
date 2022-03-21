<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Support;

final class Language
{
    public const FLUENCY_GREAT = 'great';
    public const FLUENCY_GOOD = 'good';

    public function __construct(
        private string $code,
        private string|null $fluency,
    ) {
    }

    /**
     * The language in ISO 639-1 format.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    public function getFluency(): string|null
    {
        return $this->fluency;
    }
}
