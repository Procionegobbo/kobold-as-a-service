<?php

describe('Throttle bypass middleware', function () {

    test('valid bypass key skips the rate limiter and returns 200', function () {
        config(['app.throttle_bypassers' => ['secret-key']]);

        $response = $this->postJson(
            route('kobold.generate'),
            ['language' => 'en'],
            ['X-Bypass-Key' => 'secret-key'],
        );

        $response->assertOk();
    });

    test('second consecutive request with valid bypass key also returns 200', function () {
        config(['app.throttle_bypassers' => ['secret-key']]);

        $this->postJson(route('kobold.generate'), ['language' => 'en'], ['X-Bypass-Key' => 'secret-key']);

        $response = $this->postJson(
            route('kobold.generate'),
            ['language' => 'en'],
            ['X-Bypass-Key' => 'secret-key'],
        );

        $response->assertOk();
    });

    test('invalid bypass key does not skip the rate limiter', function () {
        config(['app.throttle_bypassers' => ['secret-key']]);

        $response = $this->postJson(
            route('kobold.generate'),
            ['language' => 'en'],
            ['X-Bypass-Key' => 'wrong-key'],
        );

        $response->assertOk();
    });

    test('absent X-Bypass-Key header does not skip the rate limiter', function () {
        config(['app.throttle_bypassers' => ['secret-key']]);

        $response = $this->postJson(route('kobold.generate'), ['language' => 'en']);

        $response->assertOk();
    });

    test('bypass is impossible when THROTTLE_BYPASSERS is empty', function () {
        config(['app.throttle_bypassers' => []]);

        $response = $this->postJson(
            route('kobold.generate'),
            ['language' => 'en'],
            ['X-Bypass-Key' => 'any-key'],
        );

        $response->assertOk();
    });

    test('one of multiple configured keys is accepted', function () {
        config(['app.throttle_bypassers' => ['key-one', 'key-two', 'key-three']]);

        $response = $this->postJson(
            route('kobold.generate'),
            ['language' => 'en'],
            ['X-Bypass-Key' => 'key-two'],
        );

        $response->assertOk();
    });

    test('whitespace-padded key in config is trimmed and matched correctly', function () {
        config(['app.throttle_bypassers' => ['trimmed-key']]);

        $response = $this->postJson(
            route('kobold.generate'),
            ['language' => 'en'],
            ['X-Bypass-Key' => 'trimmed-key'],
        );

        $response->assertOk();
    });

});
