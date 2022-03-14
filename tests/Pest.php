<?php

use Worksome\Ceevee\Tests\TestCase;

uses(TestCase::class)->in('Feature');

function fakeCVFilePath(): string
{
    return __DIR__ . '/Stubs/CVs/john_williams.jpg';
}

function fakeCVContent(): string
{
    return file_get_contents(fakeCVFilePath());
}
