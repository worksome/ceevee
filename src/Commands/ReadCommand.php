<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Commands;

use Illuminate\Console\Command;
use Worksome\Ceevee\Facades\Ceevee;
use Worksome\Ceevee\Support\Content;
use Worksome\Ceevee\Support\Language;
use Worksome\Ceevee\Support\Skill;

class ReadCommand extends Command
{
    public $signature = 'ceevee:read
        {resume : a path to the CV file you\'d like to parse.}';

    public $description = 'Read a CV and output details about it.';

    public function handle(): int
    {
        $filePath = strval($this->argument('resume'));

        $this->info("Retrieving details for resume [{$filePath}]...");

        $details = Ceevee::read(Content::fromPath($filePath));

        $this->newLine(2);

        $this->line('Email address:');
        $this->info($details->contactInformation()->getEmailAddress() ?? 'Unknown');
        $this->line('Phone number:');
        $this->info($details->contactInformation()->getPrimaryNumber() ?? 'Unknown');

        $this->newLine();
        $this->info($details->profilePicture() ? 'Profile picture found.' : 'No profile picture found.');

        $this->newLine();
        $this->line('Summary:');
        $this->info($details->summary() ?? 'Unknown');

        $this->newLine();
        $this->line('Months of experience:');
        $this->info(strval($details->monthsOfExperience()) ?: 'Unknown');

        $this->newLine();
        $this->line('Skills:');
        $this->info(collect($details->skills())->map(fn (Skill $skill) => $skill->getName())->join(',', ' and '));

        $this->newLine();
        $this->line('Links:');
        foreach ($details->links() as $link) {
            $this->info("- {$link->getUrl()}");
        }

        $this->newLine();
        $this->line('Education:');
        foreach ($details->education() as $education) {
            $this->info("- {$education->getName()}, {$education->getFromYear()} - {$education->getToYear()}");
        }

        $this->newLine();
        $this->line('Languages spoken:');
        $this->info(collect($details->languagesSpoken())->map(fn (Language $language) => $language->getCode())->join(',', ' and '));

        return self::SUCCESS;
    }
}
