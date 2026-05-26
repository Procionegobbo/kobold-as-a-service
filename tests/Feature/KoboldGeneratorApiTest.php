<?php

use App\Services\KoboldGeneratorService;

$italianKeys = [
    'Nomekoboldo',
    'CogNomekoboldo',
    'Originekoboldo',
    'Colorekoboldo',
    'SegnoParticolare',
    'ParentelaKoboldo',
    'PiattoKoboldo',
    'StoriaKoboldo',
    'MestiereKoboldo',
];

$englishKeys = [
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

describe('POST /api/generate-kobold', function () use ($italianKeys, $englishKeys) {
    test('empty body returns 200 with Italian keys', function () use ($italianKeys) {
        $response = $this->postJson(route('kobold.generate'));

        $response->assertOk()
            ->assertJsonStructure($italianKeys);
    });

    test('language it returns 200 with Italian keys', function () use ($italianKeys) {
        $response = $this->postJson(route('kobold.generate'), ['language' => 'it']);

        $response->assertOk()
            ->assertJsonStructure($italianKeys);
    });

    test('language en returns 200 with English keys', function () use ($englishKeys) {
        $response = $this->postJson(route('kobold.generate'), ['language' => 'en']);

        $response->assertOk()
            ->assertJsonStructure($englishKeys);
    });

    test('unknown language fr falls back to Italian keys', function () use ($italianKeys) {
        $response = $this->postJson(route('kobold.generate'), ['language' => 'fr']);

        $response->assertOk()
            ->assertJsonStructure($italianKeys);
    });

    test('language ita fails validation', function () {
        $response = $this->postJson(route('kobold.generate'), ['language' => 'ita']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['language']);
    });

    test('language 1t fails validation', function () {
        $response = $this->postJson(route('kobold.generate'), ['language' => '1t']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['language']);
    });

    test('language 42 fails validation', function () {
        $response = $this->postJson(route('kobold.generate'), ['language' => '42']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['language']);
    });

    test('runtime exception returns 500 grammar error', function () {
        $this->mock(KoboldGeneratorService::class)
            ->shouldReceive('generate')
            ->andThrow(new RuntimeException);

        $response = $this->postJson(route('kobold.generate'), ['language' => 'it']);

        $response->assertStatus(500)
            ->assertJson(['error' => 'Grammar file could not be loaded.']);
    });

    test('json exception returns 500 invalid json error', function () {
        $this->mock(KoboldGeneratorService::class)
            ->shouldReceive('generate')
            ->andThrow(new JsonException);

        $response = $this->postJson(route('kobold.generate'), ['language' => 'it']);

        $response->assertStatus(500)
            ->assertJson(['error' => 'Generated output was not valid JSON.']);
    });
});
