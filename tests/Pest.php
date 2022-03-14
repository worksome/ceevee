<?php

use Worksome\Ceevee\Tests\TestCase;

uses(TestCase::class)->in(__DIR__ . '/Feature');

function fakeCVFilePath(): string
{
    return __DIR__ . '/Stubs/CVs/hannah_mills.pdf';
}

function fakeCVContent(): string
{
    return file_get_contents(fakeCVFilePath());
}
