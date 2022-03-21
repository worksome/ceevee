<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Parsers\SovrenParser;

use Worksome\Ceevee\Support\Language;

final class LanguageParser
{
    public function __construct(private array $details)
    {
    }

    /**
     * @return array<int, Language>
     */
    public function __invoke(): array
    {
        $primaryLanguage = $this->getPrimaryLanguage();

        $additionalLanguages = collect(data_get($this->details, 'StructuredXMLResume.Languages.Language', []))
            ->filter(fn (mixed $details) => is_array($details))
            ->map(fn (array $details) => $this->buildLanguage($details['LanguageCode'], $details['Comments']))
            ->filter(fn (Language|null $language) => $language?->getCode() !== $primaryLanguage?->getCode())
            ->values();

        return array_filter([$primaryLanguage, ...$additionalLanguages]);
    }

    private function getPrimaryLanguage(): Language|null
    {
        $nodesToCheck = [
            'UserArea.sov:ResumeUserArea.sov:PersonalInformation.sov:MotherTongue',
            'UserArea.sov:ResumeUserArea.sov:Culture.sov:Language',
        ];

        /** @var string|null $primaryLanguageCode */
        $primaryLanguageCode = collect($nodesToCheck)
            ->map(fn (string $key) => data_get($this->details, $key))
            ->filter()
            ->first();

        return $this->buildLanguage($primaryLanguageCode, Language::FLUENCY_GREAT);
    }

    private function buildLanguage(string|null $languageCode, string|null $comments): Language|null
    {
        if ($languageCode === null) {
            return null;
        }

        $comments = strtolower($comments ?? '');

        $fluency = match (true) {
            str_contains($comments, 'fluent'),
            str_contains($comments, 'mother'),
            str_contains($comments, 'native'),
            $comments === Language::FLUENCY_GREAT => Language::FLUENCY_GREAT,
            default => Language::FLUENCY_GOOD,
        };

        return new Language($languageCode, $fluency);
    }
}
