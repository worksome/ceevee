<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Support;

final class Education
{
    public function __construct(
        private readonly string|null $name,
        private readonly string|null $fromYear,
        private readonly string|null $toYear,
        private readonly string|null $degree,
        private readonly string|null $field,
    ) {
    }

    public function getName(): string|null
    {
        return $this->name;
    }

    public function getFromYear(): string|null
    {
        return $this->fromYear;
    }

    public function getToYear(): string|null
    {
        return $this->toYear;
    }

    public function getDegree(): string|null
    {
        return $this->degree;
    }

    public function getField(): string|null
    {
        return $this->field;
    }
}
