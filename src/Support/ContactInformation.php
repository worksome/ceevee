<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Support;

final class ContactInformation
{
    public function __construct(
        private readonly string|null $addressLine,
        private readonly string|null $municipality,
        private readonly string|null $postalCode,
        private readonly string|null $countryCode,
        private readonly string|null $telephoneNumber,
        private readonly string|null $mobileNumber,
        private readonly string|null $emailAddress,
    ) {
    }

    public function getAddressLine(): string|null
    {
        return $this->addressLine;
    }

    public function getMunicipality(): string|null
    {
        return $this->municipality;
    }

    public function getPostalCode(): string|null
    {
        return $this->postalCode;
    }

    public function getCountryCode(): string|null
    {
        return $this->countryCode;
    }

    public function getTelephoneNumber(): string|null
    {
        return $this->telephoneNumber;
    }

    public function getMobileNumber(): string|null
    {
        return $this->mobileNumber;
    }

    public function getPrimaryNumber(): string|null
    {
        return $this->getMobileNumber() ?? $this->getTelephoneNumber();
    }

    public function getEmailAddress(): string|null
    {
        return $this->emailAddress;
    }
}
