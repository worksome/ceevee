<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Testing;

use Worksome\Ceevee\Support\CVDetail;
use Worksome\Ceevee\Support\Link;
use Worksome\Ceevee\Support\Skill;

final class CVDetailFactory
{
    private int $monthsOfExperience = 0;

    private string $summary = 'Administrative support professional offering versatile office management skills and proficiency in Microsoft Office programs.';

    /**
     * @var array<int, Skill>|null
     */
    private array|null $skills = null;

    /**
     * @var array<int, Link>
     */
    private array $links = [];

    private string|null $profilePicture = null;

    public static function new(): self
    {
        return new self();
    }

    public function withMonthsOfExperience(int $years): self
    {
        $this->monthsOfExperience = $years;

        return $this;
    }

    public function withSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @param array<int, Skill> $skills
     */
    public function withSkills(array $skills): self
    {
        $this->skills = $skills;

        return $this;
    }

    public function withLinks(Link ...$links): self
    {
        $this->links = $links;

        return $this;
    }

    public function withProfilePicture(string $filePath): self
    {
        $this->profilePicture = base64_encode(file_get_contents($filePath));

        return $this;
    }

    public function create(): CVDetail
    {
        return new CVDetail(
            $this->buildSkills(),
            $this->monthsOfExperience,
            $this->summary,
            $this->links,
            $this->profilePicture,
        );
    }

    /**
     * @return array<int, Skill>
     */
    private function buildSkills(): array
    {
        if ($this->skills !== null) {
            return $this->skills;
        }

        return [
            (new Skill('Administrative or Clerical'))
                ->hasPercentageOfParent(60)
                ->addSubSkills(
                    (new Skill('Document-centric'))
                        ->hasPercentageOfParent(70)
                        ->addSubSkills(
                            new Skill('Correspondence'),
                            new Skill('Data Entry'),
                            new Skill('Filing'),
                            new Skill('Keyboarding'),
                        ),
                    (new Skill('Document-centric'))
                        ->hasPercentageOfParent(30)
                        ->addSubSkills(new Skill('Calendaring')),
                ),
            (new Skill('Information Technology'))
                ->hasPercentageOfParent(40)
                ->addSubSkills(
                    (new Skill('Database'))
                        ->hasPercentageOfParent(100)
                        ->addSubSkills(
                            new Skill('Database'),
                            (new Skill('Microsoft Access'))->addSubSkills(new Skill('MS Access')),
                        ),
                ),
        ];
    }
}
