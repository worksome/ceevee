<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Parsers;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\RequestException;
use InvalidArgumentException;
use Worksome\Ceevee\Contracts\Parser;
use Worksome\Ceevee\Support\CVDetail;
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
            ->filter(fn (array $details) => array_key_exists('InternetWebAddress', $details))
            ->map(fn (array $details) => new Link($details['Use'] ?? $details['InternetWebAddress'], $details['InternetWebAddress']));

        return $directLinks->merge($linksFromContactMethods)->filter()->all();
    }

    private function getProfilePicture(array $details): string|null
    {
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
