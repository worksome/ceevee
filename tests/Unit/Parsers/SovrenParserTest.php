<?php

declare(strict_types=1);

use Illuminate\Http\Client\Factory;
use Worksome\Ceevee\Support\ContactInformation;
use Worksome\Ceevee\Support\Education;
use Worksome\Ceevee\Support\Skill;

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
            fn($skill) => $skill->percentageOfParent()->toBe(50),
            fn($skill) => $skill->percentageOfParent()->toBe(23),
            fn($skill) => $skill->percentageOfParent()->toBe(7),
            fn($skill) => $skill->percentageOfParent()->toBe(6),
            fn($skill) => $skill->percentageOfParent()->toBe(5),
            fn($skill) => $skill->percentageOfParent()->toBe(5),
            fn($skill) => $skill->percentageOfParent()->toBe(5),
            fn($skill) => $skill->percentageOfParent()->toBe(0),
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
    $skills = sovrenParser('Sean Skilless')
        ->parse(fakeCVContent())
        ->skills();

    expect($skills)->toHaveCount(0);
});

it('can return months of experience', function (string $name, int $expectedMonthsOfExperience) {
    $experience = sovrenParser($name)
        ->parse(fakeCVContent())
        ->monthsOfExperience();

    expect($experience)->toBe($expectedMonthsOfExperience);
})->with([
    ['Evan Experienced', 72],
    ['Hannah Mills', 0],
]);

it('can return links correctly', function () {
    $links = sovrenParser('Oliver Nybroe')
        ->parse(fakeCVContent('Oliver Nybroe'))
        ->links();

    expect($links)
        ->toHaveCount(7)
        ->sequence(
        // The first 4 links come from actual discovered links
            fn($link) => $link->getName()->toBe('www.linkedin.com'),
            fn($link) => $link->getName()->toBe('github.com/olivernybroe'),
            fn($link) => $link->getName()->toBe('pcservicecenter.dk'),
            fn($link) => $link->getName()->toBe('www.linkedin.com'),
            // The last 3 links come from contact methods
            fn($link) => $link->getName()->toBe('linkedIn'),
            fn($link) => $link->getName()->toBe('github'),
            fn($link) => $link->getName()->toBe('linkedIn'),
        );
});

it('will return an empty array for `links()` when a CV has no links', function () {
    $links = sovrenParser('Hannah Mills')
        ->parse(fakeCVContent('Hannah Mills'))
        ->links();

    expect($links)->toBeArray()->toBeEmpty();
});

it('can return the base64 encoded profile picture', function () {
    $sovren = sovrenParser(
        'Han Boetes',
        options: [
            'OutputCandidateImage' => true
        ],
    );

    $image = $sovren->parse(fakeCVContent('Han Boetes'))->profilePicture();

    expect($image)->toBeBase64EncodedImage();
});

it('will return null for the profile picture of CVs with no provided image', function () {
    $sovren = sovrenParser(
        'Hannah Mills',
        options: [
            'OutputCandidateImage' => true
        ],
    );

    $image = $sovren->parse(fakeCVContent('Hannah Mills'))->profilePicture();

    expect($image)->toBeNull();
});

it('can return an education array', function () {
    $education = sovrenParser('Hannah Mills')
        ->parse(fakeCVContent('Hannah Mills'))
        ->education();

    expect($education)
        ->ray()
        ->toBeArray()->toHaveCount(1)
        ->{0}->toBeInstanceOf(Education::class)
        ->{0}->getName()->toBe('SOUTH LONDON COLLEGE');
});

it('can correctly parse degrees as part of education', function () {
    $education = sovrenParser('Oliver Nybroe')
        ->parse(fakeCVContent('Oliver Nybroe'))
        ->education();

    expect($education)
        ->toBeArray()->toHaveCount(3)
        ->sequence(
            fn($uni) => $uni->getDegree()->toBe('Bachelors'),
            fn($udacity) => $udacity->getDegree()->toBeNull(),
            fn($uni) => $uni->getDegree()->toBe('Bachelors'),
        );

    expect($education[0])
        ->getFromYear()->toBe('2016')
        ->getToYear()->toBe('2020');
});

it('can return contact information', function (string $name, $expectations) {
    $contactInformation = sovrenParser($name)
        ->parse(fakeCVContent($name))
        ->contactInformation();

    $expectations(expect($contactInformation)->toBeInstanceOf(ContactInformation::class));
})->with([
    [
        'Oliver Nybroe',
        fn() => fn ($information) => $information
            ->getMunicipality()->toBe('Copenhagen')
            ->getCountryCode()->toBe('DK')
            ->getEmailAddress()->toBe('olivernybroe@gmail.com')
    ],
    [
        'Han Boetes',
        fn() => fn ($information) => $information
            ->getTelephoneNumber()->toBe('+43 6 8181 5268 21')
            ->getEmailAddress()->toBe('hboetes@gmail.com')
    ],
    [
        'Hannah Mills',
        fn() => fn($information) => $information
            ->getAddressLine()->toBe('189 Chobham Gardens')
            ->getMunicipality()->toBe('Putney')
            ->getCountryCode()->toBe('UK')
            ->getMobileNumber()->toBe('077777722')
            ->getEmailAddress()->toBe('hannah.mills@gmailing.com')
    ]
]);

it('throws an exception when using an unsupported region', function () {
    sovrenParser(region: 'foo')->parse(fakeCVContent());
})->throws(InvalidArgumentException::class);
