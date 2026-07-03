<?php

use App\Http\Requests\KlantZoekRequest;

test('postcode wordt genormaliseerd', function (): void {
    $request = KlantZoekRequest::create('/klanten', 'GET', [
        'postcode' => '3512 ab',
    ]);

    expect($request->postcode())->toBe('3512AB');
});

test('lege postcode blijft null', function (): void {
    $request = KlantZoekRequest::create('/klanten', 'GET', [
        'postcode' => '   ',
    ]);

    expect($request->postcode())->toBeNull();
});

test('postcode validatieregel en melding zijn aanwezig', function (): void {
    $request = new KlantZoekRequest;

    expect($request->rules()['postcode'])
        ->toContain('nullable')
        ->toContain('max:10')
        ->toContain('regex:/^[1-9][0-9]{3}\s?[A-Za-z]{2}$/')
        ->and($request->messages()['postcode.regex'])
        ->toBe('Voer een geldige Nederlandse postcode in, bijvoorbeeld 3512AB.');
});
