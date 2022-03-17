<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Parsers\SovrenParser;

use Worksome\Ceevee\Support\Skill;

final class SkillsParser
{
    public function __construct(private array $details)
    {
    }

    /**
     * @return array<int, Skill>
     */
    public function __invoke(): array
    {
        $rawSkills = data_get($this->details, 'UserArea.sov:ResumeUserArea.sov:ExperienceSummary.sov:SkillsTaxonomyOutput.sov:TaxonomyRoot.0.sov:Taxonomy');

        return collect($rawSkills)
            ->map(fn(array $skillDetail) => $this->buildSkill($skillDetail))
            ->filter()
            ->all();
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
