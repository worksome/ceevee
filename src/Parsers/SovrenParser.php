<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Parsers;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\RequestException;
use InvalidArgumentException;
use Worksome\Ceevee\Contracts\Parser;
use Worksome\Ceevee\Parsers\SovrenParser\ContactInformationParser;
use Worksome\Ceevee\Parsers\SovrenParser\EducationParser;
use Worksome\Ceevee\Parsers\SovrenParser\EmploymentHistoryParser;
use Worksome\Ceevee\Parsers\SovrenParser\LanguageParser;
use Worksome\Ceevee\Parsers\SovrenParser\LinksParser;
use Worksome\Ceevee\Parsers\SovrenParser\SkillsParser;
use Worksome\Ceevee\Support\CVDetail;

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
        $response = json_decode(strval(data_get($baseRequest, 'Value.ParsedDocument', '[]')), true);

        if (! is_array($response)) {
            $this->failBecauseOfBadResponse('The response from Sovren was malformed.');
        }

        if (! array_key_exists('Resume', $response)) {
            $this->failBecauseOfBadResponse('No [Resume] field was detected.');
        }

        $details = $response['Resume'];

        if (! is_array($details)) {
            $this->failBecauseOfBadResponse();
        }

        return new CVDetail(
            (new SkillsParser($details))(),
            $this->getMonthsOfExperience($details),
            $this->getSummary($details),
            (new LinksParser($details))(),
            $baseRequest['Value']['CandidateImage'] ?? null,
            (new EducationParser($details))(),
            (new ContactInformationParser($details))(),
            (new EmploymentHistoryParser($details))(),
            (new LanguageParser($details))(),
            $baseRequest,
        );
    }

    private function getMonthsOfExperience(array $details): int
    {
        return intval(
            data_get($details, 'UserArea.sov:ResumeUserArea.sov:ExperienceSummary.sov:MonthsOfWorkExperience')
        );
    }

    private function getSummary(array $details): string|null
    {
        /** @var string|null $summary */
        $summary = data_get($details, 'StructuredXMLResume.ExecutiveSummary');

        return $summary !== null
            ? strval(str_replace(["\r\n", "\r", "\n"], ' ', $summary))
            : null;
    }

    /**
     * @throws RequestException
     */
    private function makeRequest(string $content): array
    {
        $data = $this
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

        if (! is_array($data)) {
            throw new InvalidArgumentException('The response from Sovren was of an unexpected format.');
        }

        return $data;
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
            default => throw new InvalidArgumentException(
                "[{$this->region}] is not a supported Sovren region. Please use 'eu', 'us' or 'au'."
            ),
        };
    }

    /**
     * @return never-return
     */
    private function failBecauseOfBadResponse(string|null $reason = null): void
    {
        throw new InvalidArgumentException("The response from Sovren could not be parsed. $reason");
    }
}
