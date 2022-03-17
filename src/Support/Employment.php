<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Support;

final class Employment
{
    public function __construct(
        private string|null $type,
        private string|null $company,
        private string|null $name,
        private string|null $description,
        private string|null $municipality,
        private string|null $countryCode,
        private string|null $startDate,
        private string|null $endDate,
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
