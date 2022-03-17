<?php

use Illuminate\Support\Str;
use Worksome\Ceevee\Parsers\SovrenParser;
use Worksome\Ceevee\Tests\Factories\Http\Sovren\SovrenHttpFactory;
use Worksome\Ceevee\Tests\TestCase;
use Illuminate\Http\Client\Factory;

uses(TestCase::class)->in(__DIR__ . '/Feature');

// Test functions

function fakeCVFilePath(string $for = 'Hannah Mills'): string
{
    $name = Str::snake($for);

    return __DIR__ . "/Stubs/CVs/{$name}.pdf";
}

function fakeCVContent(string $for = 'Hannah Mills'): string
{
    return file_get_contents(fakeCVFilePath($for));
}

function sovrenParser(Factory|null $client = null, string $region = 'eu', array $options = []): SovrenParser
{
    return new SovrenParser(
        $client ?? SovrenHttpFactory::new()->create(),
        $_ENV['SOVREN_ACCOUNT_ID'] ?? '1234',
        $_ENV['SOVREN_SERVICE_KEY'] ?? 'password',
        $region,
        $options,
    );
}

// Expectation extensions

expect()->extend('toBeBase64EncodedImage', function () {
    expect($this->value)->toBeString();
    expect(imagecreatefromstring(base64_decode($this->value)))->not->toBeFalse();
    expect(getimagesizefromstring(base64_decode($this->value)))
        ->toBeArray()
        ->not->toBeEmpty();

    return $this;
});
