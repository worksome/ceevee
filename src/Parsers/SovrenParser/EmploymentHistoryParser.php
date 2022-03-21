<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Parsers\SovrenParser;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Worksome\Ceevee\Support\Employment;

final class EmploymentHistoryParser
{
    public function __construct(private array $details)
    {
    }

    /**
     * @return array<int, Employment>
     */
    public function __invoke(): array
    {
        $employmentHistory = data_get($this->details, 'StructuredXMLResume.EmploymentHistory.EmployerOrg', []);

        return collect($employmentHistory)
            ->filter(fn ($detail) => is_array($detail))
            ->map(fn(array $detail) => $this->buildEmployment($detail))
            ->filter()
            ->all();
    }

    private function buildEmployment(array $detail): Employment|null
    {
        if (data_get($detail, 'PositionHistory.0.Title') === null) {
            return null;
        }

        $startDate = $this->parseDate(data_get($detail, 'PositionHistory.0.StartDate'));
        $endDate = $this->parseDate(data_get($detail, 'PositionHistory.0.EndDate'));

        return new Employment(
            // @phpstan-ignore-next-line
            data_get($detail, 'PositionHistory.0.@positionType'),
            // @phpstan-ignore-next-line
            data_get($detail, 'EmployerOrgName'),
            // @phpstan-ignore-next-line
            data_get($detail, 'PositionHistory.0.Title'),
            // @phpstan-ignore-next-line
            data_get($detail, 'PositionHistory.0.Description'),
            // @phpstan-ignore-next-line
            data_get($detail, 'PositionHistory.0.OrgInfo.0.PositionLocation.Municipality'),
            // @phpstan-ignore-next-line
            data_get($detail, 'PositionHistory.0.OrgInfo.0.PositionLocation.CountryCode'),
            $startDate,
            $endDate,
        );
    }

    private function parseDate(mixed $dateData): string|null
    {
        if (! is_array($dateData)) {
            return null;
        }

        $date = $this->createDateString($dateData);

        if ($date === null) {
            return null;
        }

        try {
            Carbon::parse($date);
        } catch (InvalidFormatException) {
            return null;
        }

        return $date;
    }

    private function createDateString(array $dateData): string|null
    {
        if (array_key_exists('AnyDate', $dateData)) {
            return strval(data_get($dateData, 'AnyDate'));
        }

        if (array_key_exists('YearMonth', $dateData)) {
            return data_get($dateData, 'YearMonth') . '-01';
        }

        if (array_key_exists('Year', $dateData)) {
            return data_get($dateData, 'Year') . '-01-01';
        }

        return null;
    }
}
