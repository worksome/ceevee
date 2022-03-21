<?php

use Worksome\Ceevee\Facades\Ceevee;

it('reads the given resume', function () {
    Ceevee::fake();

    $this->artisan('ceevee:read', [
        'resume' => __DIR__ . '/../../Stubs/CVs/hannah_mills.pdf'
    ]);

    Ceevee::assertRead();
});
