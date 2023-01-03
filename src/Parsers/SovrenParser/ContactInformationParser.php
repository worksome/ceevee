<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Parsers\SovrenParser;

use Worksome\Ceevee\Support\ContactInformation;
use Worksome\Ceevee\Support\PhonePrefixes;

final class ContactInformationParser
{
    private array $contactParts = [
        'addressLine' => null,
        'municipality' => null,
        'postalCode' => null,
        'countryCode' => null,
        'telephoneNumber' => null,
        'mobileNumber' => null,
        'emailAddress' => null,
    ];

    public function __construct(private array $details)
    {
    }

    public function __invoke(): ContactInformation
    {
        $contactMethods = data_get($this->details, 'StructuredXMLResume.ContactInfo.ContactMethod');

        collect($contactMethods)
            ->filter(fn (mixed $details) => is_array($details))
            ->each(fn (array $details) => $this->parseArrayNode($details));

        return new ContactInformation(...$this->contactParts);
    }

    private function parseArrayNode(array $node): void
    {
        if (array_key_exists('PostalAddress', $node)) {
            $this->contactParts['addressLine'] = data_get($node, 'PostalAddress.DeliveryAddress.AddressLine.0');
            $this->contactParts['municipality'] = data_get($node, 'PostalAddress.Municipality');
            $this->contactParts['postalCode'] = data_get($node, 'PostalAddress.PostalCode');
            $this->contactParts['countryCode'] = data_get($node, 'PostalAddress.CountryCode');
        }

        if (array_key_exists('Telephone', $node)) {
            $this->contactParts['telephoneNumber'] = $this->cleanPhoneNumber(
                data_get($node, 'Telephone.FormattedNumber')
            );
        }

        if (array_key_exists('Mobile', $node)) {
            $this->contactParts['mobileNumber'] = $this->cleanPhoneNumber(data_get($node, 'Mobile.FormattedNumber'));
        }

        if (array_key_exists('InternetEmailAddress', $node)) {
            $this->contactParts['emailAddress'] = data_get($node, 'InternetEmailAddress');
        }
    }

    private function cleanPhoneNumber(mixed $number): string|null
    {
        if (! is_string($number)) {
            return null;
        }

        if (substr($number, 0, 1) === '+') {
            return $number;
        }

        $countryCode = $this->contactParts['countryCode'] ?? data_get(
            $this->details,
            'UserArea.sov:ResumeUserArea.sov:Culture.sov:Country'
        );

        if ($countryCode === null) {
            return $number;
        }

        $prefix = PhonePrefixes::for($countryCode);
        $strippedNumber = preg_replace('/\D/', '', $number);

        if ($strippedNumber === null) {
            return null;
        }

        if (substr($strippedNumber, 0, strlen($prefix)) === $prefix) {
            return $number;
        }

        return "+{$prefix} {$number}";
    }
}
