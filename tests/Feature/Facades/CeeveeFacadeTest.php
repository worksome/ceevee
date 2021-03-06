<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\ExpectationFailedException;
use Worksome\Ceevee\Facades\Ceevee;
use Worksome\Ceevee\Support\Content;
use Worksome\Ceevee\Support\CVDetail;
use Worksome\Ceevee\Testing\CVDetailFactory;

it('can read a File', function ($file) {
    expect(Ceevee::read($file))->toBeInstanceOf(CVDetail::class);
})->with([
    'File instance' => fn () => UploadedFile::fake()->createWithContent('my_cv.pdf', fakeCVContent()),
    'string path' => fn () => __DIR__ . '/../../Stubs/CVs/hannah_mills.pdf',
    'fromFile' => fn () => Content::fromFile(UploadedFile::fake()->createWithContent('my_cv.pdf', fakeCVContent())),
    'fromRaw' => fn () => Content::fromRaw(fakeCVContent()),
    'fromPath' => fn () => Content::fromPath(__DIR__ . '/../../Stubs/CVs/hannah_mills.pdf'),
]);

it('can fake CV details', function () {
    Ceevee::fake(
        CVDetailFactory::new()->withMonthsOfExperience(5)->create(),
        CVDetailFactory::new()->withMonthsOfExperience(7)->create(),
    );

    // The first request uses the first given factory.
    expect(Ceevee::read(fakeCVFilePath())->monthsOfExperience())->toBe(5);

    // The second request uses the second given factory.
    expect(Ceevee::read(fakeCVFilePath())->monthsOfExperience())->toBe(7);

    // The third request loops back to the beginning of the given sequence requests.
    expect(Ceevee::read(fakeCVFilePath())->monthsOfExperience())->toBe(5);
});

it('can assert the number of times a read occurred', function (int $timesRead) {
    Ceevee::fake();

    for ($i = 0; $i < $timesRead; $i++) {
        Ceevee::read(fakeCVFilePath());
    }

    expect(fn () => Ceevee::assertRead($timesRead))->not->toThrow(ExpectationFailedException::class);
    expect(fn () => Ceevee::assertRead($timesRead - 1))->toThrow(ExpectationFailedException::class);
    expect(fn () => Ceevee::assertRead($timesRead + 1))->toThrow(ExpectationFailedException::class);
})->with(fn () => range(2, 10));

it('can create a sovren driver', function () {
    config()->set('ceevee.default', 'sovren');

    $detail = Ceevee::read(fakeCVFilePath('Hannah Mills'));

    expect($detail)->toBeInstanceOf(CVDetail::class);
})->group('integration');
