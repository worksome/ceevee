<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Parsers;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\RequestException;
use InvalidArgumentException;
use Worksome\Ceevee\Contracts\Parser;
use Worksome\Ceevee\Support\ContactInformation;
use Worksome\Ceevee\Support\CVDetail;
use Worksome\Ceevee\Support\Education;
use Worksome\Ceevee\Support\Link;
use Worksome\Ceevee\Support\Skill;

final class SovrenParser implements Parser
{
    public function __construct(
        private Factory $client,
        private string $accountId,
        private string $serviceKey,
        private string $region = 'eu',
        private array $options = [],
    ) {
    }

    public function parse(string $content): CVDetail
    {
        $baseRequest = $this->makeRequest($content);
        $details = json_decode($baseRequest['Value']['ParsedDocument'], true)['Resume'];

        return new CVDetail(
            $this->getSkills($details),
            $this->getMonthsOfExperience($details),
            $this->getSummary($details),
            $this->getLinks($details),
            $baseRequest['Value']['CandidateImage'] ?? null,
            $this->getEducation($details),
            $this->getContactInformation($details),
            $baseRequest,
        );
    }

    /**
     * @param array<int, array<string, mixed>> $details
     * @return array<int, Skill>
     */
    private function getSkills(array $details): array
    {
        $rawSkills = data_get($details, 'UserArea.sov:ResumeUserArea.sov:ExperienceSummary.sov:SkillsTaxonomyOutput.sov:TaxonomyRoot.0.sov:Taxonomy');

        return collect($rawSkills)
            ->map(fn(array $skillDetail) => $this->buildSkill($skillDetail))
            ->filter()
            ->all();
    }

    private function getMonthsOfExperience(array $details): int
    {
        return intval(data_get($details, 'UserArea.sov:ResumeUserArea.sov:ExperienceSummary.sov:MonthsOfWorkExperience'));
    }

    private function getSummary(array $details): ?string
    {
        $summary = data_get($details, 'StructuredXMLResume.ExecutiveSummary');

        return $summary !== null
            ? str_replace(["\r\n", "\r", "\n"], ' ', $summary)
            : null;
    }

    /**
     * @return array<int, Link>
     */
    private function getLinks(array $details): array
    {
        $linkDetails = data_get($details, 'UserArea.sov:ResumeUserArea.sov:ReservedData.sov:Urls.sov:Url', []);
        $contactMethodDetails = data_get($details, 'StructuredXMLResume.ContactInfo.ContactMethod');

        $directLinks = collect($linkDetails)
            ->map(function (string $link) {
                $parts = parse_url($link);

                return $parts === false
                    ? null
                    : new Link($parts['host'] ?? $parts['path'], $link);
            });

        $linksFromContactMethods = collect($contactMethodDetails)
            ->filter(fn(array $details) => array_key_exists('InternetWebAddress', $details))
            ->map(fn(array $details) => new Link($details['Use'] ?? $details['InternetWebAddress'], $details['InternetWebAddress']));

        return $directLinks->merge($linksFromContactMethods)->filter()->all();
    }

    /**
     * @param array<string, mixed> $details
     * @return array<int, Education>
     */
    private function getEducation(array $details): array
    {
        $educationDetails = data_get($details, 'StructuredXMLResume.EducationHistory.SchoolOrInstitution');

        return collect($educationDetails)
            ->map(function (array $details) {
                $degree = data_get($details, 'Degree.0.UserArea.sov:DegreeUserArea.sov:NormalizedDegreeName') ?? data_get($details, 'Degree.0.DegreeName');

                $field = data_get($details, 'Degree.0.DegreeMajor.0.Name.0');
                $minor = data_get($details, 'Degree.0.DegreeMinor.0.Name.0');

                if ($field !== null && $minor !== null) {
                    $field .= ' (Minor in ' . ucfirst($minor) . ')';
                }

                return new Education(
                    data_get($details, 'UserArea.sov:SchoolOrInstitutionTypeUserArea.sov:NormalizedSchoolName') ?? data_get($details, 'School.0.SchoolName'),
                    data_get($details, 'Degree.0.DatesOfAttendance.0.StartDate.Year'),
                    data_get($details, 'Degree.0.DatesOfAttendance.0.EndDate.Year'),
                    $degree === null ? null : ucfirst($degree),
                    $field === null ? null : ucfirst($field),
                );
            })
            ->all();
    }

    /**
     * @param array<string, mixed> $details
     */
    public function getContactInformation(array $details): ContactInformation
    {
        $contactMethods = data_get($details, 'StructuredXMLResume.ContactInfo.ContactMethod');

        $contactParts = [
            'addressLine' => null,
            'municipality' => null,
            'postalCode' => null,
            'countryCode' => null,
            'telephoneNumber' => null,
            'mobileNumber' => null,
            'emailAddress' => null,
        ];

        collect($contactMethods)->each(function (array $details) use (&$contactParts) {
            if (array_key_exists('PostalAddress', $details)) {
                $contactParts['addressLine'] = data_get($details, 'PostalAddress.DeliveryAddress.AddressLine.0');
                $contactParts['municipality'] = data_get($details, 'PostalAddress.Municipality');
                $contactParts['postalCode'] = data_get($details, 'PostalAddress.PostalCode');
                $contactParts['countryCode'] = data_get($details, 'PostalAddress.CountryCode');
            }

            if (array_key_exists('Telephone', $details)) {
                $contactParts['telephoneNumber'] = data_get($details, 'Telephone.FormattedNumber');
            }

            if (array_key_exists('Mobile', $details)) {
                $contactParts['mobileNumber'] = data_get($details, 'Mobile.FormattedNumber');
            }

            if (array_key_exists('InternetEmailAddress', $details)) {
                $contactParts['emailAddress'] = data_get($details, 'InternetEmailAddress');
            }
        });

        return new ContactInformation(...$contactParts);
    }

    /**
     * @throws RequestException
     */
    private function makeRequest(string $content): array
    {
        return $this
            ->client
            ->baseUrl($this->getBaseUrl())
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'Sovren-AccountId' => $this->accountId,
                'Sovren-ServiceKey' => $this->serviceKey,
            ])
            ->post('parser/resume', array_merge(
                $this->getRequestOptions(),
                ['DocumentAsBase64String' => base64_encode($content)]
            ))
            ->throw()
            ->json();
    }

    /**
     * @return array<string, mixed>
     */
    private function getRequestOptions(): array
    {
        return $this->options;
    }

    private function getBaseUrl(): string
    {
        return match ($this->region) {
            'eu' => 'https://eu-rest.resumeparsing.com/v9',
            'us' => 'https://us-rest.resumeparsing.com/v9',
            'au' => 'https://au-rest.resumeparsing.com/v9',
            default => throw new InvalidArgumentException("[{$this->region}] is not a supported Sovren region. Please use 'eu', 'us' or 'au'."),
        };
    }

    private function buildSkill(array $skillDetail): Skill|null
    {
        $skill = new Skill($skillDetail['@name']);

        if (strtolower($skill->getName()) === 'no skills found') {
            return null;
        }

        if (array_key_exists('@percentOfOverall', $skillDetail)) {
            $skill->hasPercentageOfParent(intval($skillDetail['@percentOfOverall']));
        }

        if (array_key_exists('@percentOfParentTaxonomy', $skillDetail)) {
            $skill->hasPercentageOfParent(intval($skillDetail['@percentOfParentTaxonomy']));
        }

        return $skill->addSubSkills(...collect($skillDetail['sov:Subtaxonomy'] ?? $skillDetail['sov:Skill'] ?? [])->map(
            fn(array $skillDetail) => $this->buildSkill($skillDetail)
        ));
    }
}
