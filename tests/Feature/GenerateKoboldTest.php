<?php

use Polygen\Polygen;

test('generate it', function () {
    $p = Polygen::fromFile(base_path('grm/kobold_json_it.grm'));
    $result = $p->generate();

    expect($result)->toBeJson();
});

test('generate en', function () {
    $p = Polygen::fromFile(base_path('grm/kobold_json_en.grm'));
    $result = $p->generate();

    expect($result)->toBeJson();
});
