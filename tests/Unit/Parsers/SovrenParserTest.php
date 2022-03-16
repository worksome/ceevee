<?php

declare(strict_types=1);

use Illuminate\Http\Client\Factory;
use Worksome\Ceevee\Support\Skill;
use Worksome\Ceevee\Tests\Factories\Http\Sovren\SovrenHttpFactory;

it('can make an actual request to Sovren', function () {
    // We'll pass in an unmodified factory so that we make a real request.
    $details = sovrenParser(new Factory())->parse(fakeCVContent());

    expect($details)
        ->monthsOfExperience()->toBe(0)
        ->summary()->toBe("Administrative support professional offering versatile office management skills and proficiency in Microsoft Office programs. Strong planner and problem solver who readily adapts to change, works independently and exceeds expectations. Able to juggle multiple priorities and meet tight deadlines without compromising quality.");
})->group('integration');

it('correctly builds the skill set', function () {
    $skills = sovrenParser()->parse(fakeCVContent())->skills();

    expect($skills)
        ->toBeArray()
        ->toHaveCount(8)
        ->sequence(
            fn ($skill) => $skill->percentageOfParent()->toBe(50),
            fn ($skill) => $skill->percentageOfParent()->toBe(23),
            fn ($skill) => $skill->percentageOfParent()->toBe(7),
            fn ($skill) => $skill->percentageOfParent()->toBe(6),
            fn ($skill) => $skill->percentageOfParent()->toBe(5),
            fn ($skill) => $skill->percentageOfParent()->toBe(5),
            fn ($skill) => $skill->percentageOfParent()->toBe(5),
            fn ($skill) => $skill->percentageOfParent()->toBe(0),
        );

    expect($skills[0])
        ->toBeInstanceOf(Skill::class)
        ->getName()->toBe('Administrative or Clerical')
        ->isSet()->toBeTrue()
        ->getSubSkills()->toHaveCount(6)->each->toBeInstanceOf(Skill::class)
        ->getSubSkills()->{0}->getSubSkills()->{0}->isSet()->toBeFalse();
});

it('will not include skills called "no skills found"', function () {
    /**
     * It has been known for Sovren to return a skill called "no skills found".
     * That's useless information for an actual Skill object, so it should
     * be skipped when building skills. This test ensures that happens.
     */
    $skills = sovrenParser(SovrenHttpFactory::new()->for('Sean Skilless')->create())
        ->parse(fakeCVContent())
        ->skills();

    expect($skills)->toHaveCount(0);
});

it('can return months of experience', function (string $name, int $expectedMonthsOfExperience) {
    $experience = sovrenParser(SovrenHttpFactory::new()->for($name)->create())
        ->parse(fakeCVContent())
        ->monthsOfExperience();

    expect($experience)->toBe($expectedMonthsOfExperience);
})->with([
    ['Evan Experienced', 72],
    ['Hannah Mills', 0],
]);

it('can return links correctly', function () {
    $links = sovrenParser(SovrenHttpFactory::new()->for('Oliver Nybroe')->create())
        ->parse(fakeCVContent('Oliver Nybroe'))
        ->links();

    expect($links)
        ->toHaveCount(4)
        ->sequence(
            fn ($link) => $link->getName()->toBe('www.linkedin.com'),
            fn ($link) => $link->getName()->toBe('github.com/olivernybroe'),
            fn ($link) => $link->getName()->toBe('pcservicecenter.dk'),
            fn ($link) => $link->getName()->toBe('www.linkedin.com'),
        );
});

it('will return an empty array for links on CVs with no link', function () {
    $links = sovrenParser(SovrenHttpFactory::new()->for('Hannah Mills')->create())
        ->parse(fakeCVContent('Hannah Mills'))
        ->links();

    expect($links)->toBeArray()->toBeEmpty();
});

it('throws an exception when using an unsupported region', function () {
    sovrenParser(region: 'foo')->parse(fakeCVContent());
})->throws(InvalidArgumentException::class);
