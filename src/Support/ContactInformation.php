<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Support;

final class ContactInformation
{
    public function __construct(
        private string|null $addressLine,
        private string|null $municipality,
        private string|null $postalCode,
        private string|null $countryCode,
        private string|null $telephoneNumber,
        private string|null $mobileNumber,
        private string|null $emailAddress,
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

    public function getEmailAddress(): string|null
    {
        return $this->emailAddress;
    }
}
