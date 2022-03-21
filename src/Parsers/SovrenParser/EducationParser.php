<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Parsers\SovrenParser;

use Worksome\Ceevee\Support\Education;

final class EducationParser
{
    public function __construct(private array $details)
    {
    }

    /**
     * @return array<int, Education>
     */
    public function __invoke(): array
    {
        $educationDetails = data_get($this->details, 'StructuredXMLResume.EducationHistory.SchoolOrInstitution');

        return collect($educationDetails)
            ->filter(fn(mixed $details) => is_array($details))
            ->map(fn(array $details) => $this->buildEducation($details))
            ->all();
    }

    /**
     * @param array<string, mixed> $details
     */
    private function buildEducation(array $details): Education
    {
        /** @var string|null $degree */
        $degree = data_get($details, 'Degree.0.UserArea.sov:DegreeUserArea.sov:NormalizedDegreeName') ?? data_get($details, 'Degree.0.DegreeName');
        /** @var string|null $field */
        $field = data_get($details, 'Degree.0.DegreeMajor.0.Name.0');

        /** @var string|null $minor */
        $minor = data_get($details, 'Degree.0.DegreeMinor.0.Name.0');

        if ($field !== null && $minor !== null) {
            $field .= ' (Minor in ' . ucfirst($minor) . ')';
        }

        return new Education(
            // @phpstan-ignore-next-line
            data_get($details, 'UserArea.sov:SchoolOrInstitutionTypeUserArea.sov:NormalizedSchoolName') ?? data_get($details, 'School.0.SchoolName'),
            // @phpstan-ignore-next-line
            data_get($details, 'Degree.0.DatesOfAttendance.0.StartDate.Year'),
            // @phpstan-ignore-next-line
            data_get($details, 'Degree.0.DatesOfAttendance.0.EndDate.Year'),
            $degree === null ? null : ucfirst($degree),
            $field === null ? null : ucfirst($field),
        );
    }
}
