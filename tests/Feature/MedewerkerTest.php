<?php

use App\Models\User;
use App\Models\Medewerker;
use App\Models\Contact;
use Carbon\Carbon;

test('owner can view medewerkers list', function () {
    $owner = User::where('role', 'eigenaar')->first();

    $response = $this->actingAs($owner)
        ->get(route('medewerkers.index'));

    $response->assertStatus(200);
    $response->assertSee('Overzicht medewerkers');
    $response->assertSee('Gevonden medewerkers');
});

test('filtering medewerkers by specialization', function () {
    $owner = User::where('role', 'eigenaar')->first();

    // Permanent specialisation has 0 employees
    $response = $this->actingAs($owner)
        ->get(route('medewerkers.index', ['specialisatie' => 'Permanent']));

    $response->assertStatus(200);
    $response->assertSee('Er zijn geen medewerkers bekend met de geselecteerde specialisatie');
});

test('owner can view medewerker detail page', function () {
    $owner = User::where('role', 'eigenaar')->first();
    $medewerker = Medewerker::first();

    $response = $this->actingAs($owner)
        ->get(route('medewerkers.show', $medewerker->Id));

    $response->assertStatus(200);
    $response->assertSee('Medewerkerdetail');
    $response->assertSee($medewerker->naam);
});

test('owner can view edit page and update medewerker info', function () {
    $owner = User::where('role', 'eigenaar')->first();
    $medewerker = Medewerker::first();
    $contact = $medewerker->contact;

    $response = $this->actingAs($owner)
        ->get(route('medewerkers.edit', $medewerker->Id));

    $response->assertStatus(200);

    $updateData = [
        'naam' => 'Test Medewerker',
        'specialisatie' => 'Knippen',
        'geboortedatum' => '12-04-1988',
        'email' => 'test@kniplokettiko.nl',
        'straatnaam' => 'Teststraat',
        'huisnummer' => '12',
        'toevoeging' => 'B',
        'postcode' => '1234AB',
        'plaats' => 'Teststad',
        'mobiel' => '0612345678',
        'opmerking' => 'Dit is een test.',
    ];

    $response = $this->actingAs($owner)
        ->put(route('medewerkers.update', $medewerker->Id), $updateData);

    $response->assertRedirect(route('medewerkers.show', $medewerker->Id));
    
    // Check if updated in database
    $medewerker->refresh();
    expect($medewerker->Voornaam)->toBe('Test');
    expect($medewerker->Achternaam)->toBe('Medewerker');
    expect($medewerker->contact->Straatnaam)->toBe('Teststraat');
    expect($medewerker->contact->Email)->toBe('test@kniplokettiko.nl');
});

test('minor cannot be assigned Permanent specialization', function () {
    $owner = User::where('role', 'eigenaar')->first();
    $medewerker = Medewerker::first();

    // Romy Jacobs in seed data is born on 15-01-2010. She is a minor.
    // Let's modify a medewerker birthday to make them a minor (e.g. 16 years old) and try to set to Permanent.
    $birthDate = Carbon::now()->subYears(16)->format('d-m-Y');

    $updateData = [
        'naam' => 'Minor Medewerker',
        'specialisatie' => 'Permanent',
        'geboortedatum' => $birthDate,
        'email' => 'minor@kniplokettiko.nl',
        'straatnaam' => 'Teststraat',
        'huisnummer' => '12',
        'toevoeging' => '',
        'postcode' => '1234AB',
        'plaats' => 'Teststad',
        'mobiel' => '0612345678',
        'opmerking' => 'Minor permanent attempt.',
    ];

    $response = $this->actingAs($owner)
        ->from(route('medewerkers.edit', $medewerker->Id))
        ->put(route('medewerkers.update', $medewerker->Id), $updateData);

    $response->assertRedirect(route('medewerkers.edit', $medewerker->Id));
    $response->assertSessionHasErrors(['specialisatie']);
    $response->assertSessionHas('error', 'Medewerkergegevens zijn niet bijgewerkt.');
});
