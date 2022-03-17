<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Support;

final class CVDetail
{
    /**
     * @param array<int, Skill> $skills
     * @param array<int, Link> $links
     * @param array<int, Education> $education
     * @param array<int, Employment> $employmentHistory
     */
    public function __construct(
        private array $skills,
        private int|null $monthsOfExperience,
        private string|null $summary,
        private array $links,
        private string|null $profilePicture,
        private array $education,
        private ContactInformation $contactInformation,
        private array $employmentHistory,
        private mixed $rawResponse = null,
    ) {
    }

    public function monthsOfExperience(): int|null
    {
        return $this->monthsOfExperience;
    }

    public function summary(): string|null
    {
        return $this->summary;
    }

    /**
     * @return array<int, Skill>
     */
    public function skills(): array
    {
        return $this->skills;
    }

    /**
     * @return array<int, Link>
     */
    public function links(): array
    {
        return $this->links;
    }

    /**
     * The profile picture of the applicant, if one
     * has been provided, as a base 64 encoded
     * string.
     */
    public function profilePicture(): string|null
    {
        return $this->profilePicture;
    }

    /**
     * @return array<int, Education>
     */
    public function education(): array
    {
        return $this->education;
    }

    public function contactInformation(): ContactInformation
    {
        return $this->contactInformation;
    }

    public function employmentHistory(): array
    {
        return $this->employmentHistory;
    }

    public function fullResponse(): mixed
    {
        return $this->rawResponse;
    }
}
