<?php

use App\Services\KoboldGeneratorService;

$koboldFields = [
    'KoboldName',
    'KoboldSurname',
    'KoboldOrigin',
    'KoboldColor',
    'SpecialTrait',
    'KoboldKinship',
    'KoboldDish',
    'KoboldStory',
    'KoboldJob',
];

test('homepage returns 200 OK', function () {
    $this->get(route('home'))->assertOk();
});

test('homepage view is home', function () {
    $this->get(route('home'))->assertViewIs('home');
});

test('homepage contains the project name', function () {
    $this->get(route('home'))->assertSee('Kobold As A Service');
});

test('homepage contains a link to the API endpoint', function () {
    $this->get(route('home'))->assertSee('/api/generate-kobold');
});

test('homepage contains PHP code example', function () {
    $this->get(route('home'))->assertSee('GuzzleHttp', false);
});

test('homepage contains JavaScript code example', function () {
    $this->get(route('home'))->assertSee('fetch(', false);
});

test('homepage contains Go code example', function () {
    $this->get(route('home'))->assertSee('http.Post', false);
});

test('homepage contains Python code example', function () {
    $this->get(route('home'))->assertSee('import requests', false);
});

test('homepage contains cURL code example', function () {
    $this->get(route('home'))->assertSee('curl -X POST', false);
});

test('homepage contains a live generated kobold with all nine fields', function () use ($koboldFields) {
    $response = $this->get(route('home'));

    foreach ($koboldFields as $field) {
        $response->assertSee($field, false);
    }
});

test('homepage passes a kobold array to the view', function () {
    $this->get(route('home'))
        ->assertViewHas('kobold')
        ->assertViewHas('kobold.KoboldName')
        ->assertViewHas('kobold.KoboldJob');
});

test('kobold output is cached and the service is only called once per cache window', function () use ($koboldFields) {
    $sample = array_fill_keys($koboldFields, 'sample');

    $this->mock(KoboldGeneratorService::class, function ($mock) use ($sample) {
        $mock->shouldReceive('generate')->with('en')->once()->andReturn($sample);
    });

    $this->get(route('home'))->assertOk();
    $this->get(route('home'))->assertOk();
});

test('homepage links to Polygen', function () {
    $this->get(route('home'))->assertSee('https://polygen.org/');
});

test('homepage links to FumbleGDR', function () {
    $this->get(route('home'))->assertSee('https://www.fumblegdr.it');
});

test('homepage links to polygen-php on GitHub', function () {
    $this->get(route('home'))->assertSee('https://github.com/procionegobbo/polygen-php');
});

test('homepage links to polygen-php on Packagist', function () {
    $this->get(route('home'))->assertSee('https://packagist.org/packages/procionegobbo/polygen-php');
});

test('homepage links to Laravel', function () {
    $this->get(route('home'))->assertSee('https://laravel.com');
});

test('homepage links to procionegobbo.it', function () {
    $this->get(route('home'))->assertSee('https://procionegobbo.it');
});

test('homepage footer contains attribution', function () {
    $this->get(route('home'))
        ->assertSee('Federico')
        ->assertSee('Procionegobbo');
});
