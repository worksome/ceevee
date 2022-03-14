<?php

declare(strict_types=1);

use Worksome\Ceevee\Parsers\SovrenParser;
use Illuminate\Http\Client\Factory;

it('can make an actual request to Sovren', function () {
    $sovren = new SovrenParser(
        new Factory(),
        $_ENV['SOVREN_ACCOUNT_ID'],
        $_ENV['SOVREN_SERVICE_KEY'],
    );

    $details = $sovren->parse(fakeCVContent());

    expect($details)
        ->yearsOfExperience()->toBe(0)
        ->summary()->toBe("Administrative support professional offering versatile office management skills and proficiency in Microsoft Office programs. Strong planner and problem solver who readily adapts to change, works independently and exceeds expectations. Able to juggle multiple priorities and meet tight deadlines without compromising quality.");
});
