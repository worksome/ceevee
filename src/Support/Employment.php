<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Support;

final class Employment
{
    public function __construct(
        private readonly string|null $type,
        private readonly string|null $company,
        private readonly string|null $name,
        private readonly string|null $description,
        private readonly string|null $municipality,
        private readonly string|null $countryCode,
        private readonly string|null $startDate,
        private readonly string|null $endDate,
    ) {
    }

    public function getType(): string|null
    {
        return $this->type;
    }

    public function getCompany(): string|null
    {
        return $this->company;
    }

    public function getName(): string|null
    {
        return $this->name;
    }

    public function getDescription(): string|null
    {
        return $this->description;
    }

    public function getMunicipality(): string|null
    {
        return $this->municipality;
    }

    public function getCountryCode(): string|null
    {
        return $this->countryCode;
    }

    public function getStartDate(): string|null
    {
        return $this->startDate;
    }

    public function getEndDate(): string|null
    {
        return $this->endDate;
    }
}
