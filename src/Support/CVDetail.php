<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Support;

final class CVDetail
{
    /**
     * @param array<int, Skill> $skills
     * @param array<int, Link> $links
     */
    public function __construct(
        private array $skills,
        private ?int $monthsOfExperience = null,
        private ?string $summary = null,
        private array $links = [],
        private mixed $rawResponse = null,
    ) {
    }

    public function monthsOfExperience(): ?int
    {
        return $this->monthsOfExperience;
    }

    public function summary(): ?string
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

    public function fullResponse(): mixed
    {
        return $this->rawResponse;
    }
}
