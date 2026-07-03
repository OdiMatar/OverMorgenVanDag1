<?php

use App\Http\Requests\KlantBijwerkRequest;

test('klant bijwerken normaliseert postcode en lege optionele velden', function (): void {
    $request = KlantBijwerkRequest::create('/klanten/1', 'PUT', [
        'email' => 'nieuw@example.com',
        'mobiel' => '+31 6 1234 61 74',
        'straatnaam' => 'Winkel van Sinkelstraat',
        'huisnummer' => '4',
        'postcode' => '3511 kv',
        'woonplaats' => 'Utrecht',
    ]);

    $request->setContainer(app());
    $validator = validator($request->all(), $request->rules());

    expect($validator->passes())->toBeTrue();

    $request->setValidator($validator);

    expect($request->contactgegevens())
        ->toMatchArray([
            'email' => 'nieuw@example.com',
            'postcode' => '3511KV',
            'toevoeging' => '',
            'bijzonderheden' => '',
        ]);
});

test('klant bijwerken heeft e-mail en postcode validatie', function (): void {
    $request = new KlantBijwerkRequest;

    expect($request->rules()['email'])
        ->toContain('required')
        ->toContain('email:rfc,dns')
        ->and($request->rules()['postcode'])
        ->toContain('regex:/^[1-9][0-9]{3}\s?[A-Za-z]{2}$/');
});
