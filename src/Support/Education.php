<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Support;

final class Education
{
    public function __construct(
        private string|null $name,
        private string|null $fromYear,
        private string|null $toYear,
        private string|null $degree,
        private string|null $field,
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
