<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Support;

final class Skill
{
    /**
     * @var array<int, Skill>
     */
    private array $subSkills = [];

    private int $percentageOfParent = 0;

    public function __construct(private string $name)
    {
    }

    public function addSubSkills(Skill ...$skills): self
    {
        $this->subSkills = [...$this->subSkills, ...$skills];

        return $this;
    }

    public function hasPercentageOfParent(int $percentageOfParent): self
    {
        $this->percentageOfParent = $percentageOfParent;

        return $this;
    }

    /**
     * The name of this skill. Note that top level skills
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function percentageOfParent(): int
    {
        return $this->percentageOfParent;
    }

    /**
     * @return array<int, Skill>
     */
    public function getSubSkills(): array
    {
        return $this->subSkills;
    }

    /**
     * Indicates whether this skill has sub skills.
     */
    public function isSet(): bool
    {
        return count($this->getSubSkills()) > 0;
    }
}
