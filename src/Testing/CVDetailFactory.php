<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Testing;

use InvalidArgumentException;
use Worksome\Ceevee\Support\ContactInformation;
use Worksome\Ceevee\Support\CVDetail;
use Worksome\Ceevee\Support\Education;
use Worksome\Ceevee\Support\Employment;
use Worksome\Ceevee\Support\Language;
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

    /**
     * @var array<int, Education>
     */
    private array $education = [];

    private ContactInformation|null $contactInformation = null;

    /**
     * @var array<int, Employment>
     */
    private array $employmentHistory = [];

    /**
     * @var array<int, Language>
     */
    private array $languagesSpoken = [];

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
        $this->links = array_values($links);

        return $this;
    }

    public function withProfilePicture(string $filePath): self
    {
        /** @var string $picture */
        $picture = throw_unless(
            file_get_contents($filePath),
            new InvalidArgumentException("The given file path [$filePath] could not be read."),
        );

        $this->profilePicture = base64_encode($picture);

        return $this;
    }

    public function withEducation(Education ...$education): self
    {
        $this->education = array_values($education);

        return $this;
    }

    public function withContactInformation(ContactInformation $contactInformation): self
    {
        $this->contactInformation = $contactInformation;

        return $this;
    }

    public function withEmploymentHistory(Employment ...$employment): self
    {
        $this->employmentHistory = array_values($employment);

        return $this;
    }

    public function withAbilityToSpeakIn(Language ...$languages): self
    {
        $this->languagesSpoken = array_values($languages);

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
            $this->education,
            $this->buildContactInformation(),
            $this->buildEmploymentHistory(),
            $this->languagesSpoken,
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

    private function buildContactInformation(): ContactInformation
    {
        if ($this->contactInformation !== null) {
            return $this->contactInformation;
        }

        return new ContactInformation(
            '1 Foo Street',
            'Copenhagen',
            'T3ST 1NG',
            'DK',
            '01234567890',
            '07123456789',
            'test@test.com',
        );
    }

    /**
     * @return array<int, Employment>
     */
    private function buildEmploymentHistory(): array
    {
        return $this->employmentHistory;
    }
}
