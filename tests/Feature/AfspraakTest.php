<?php

use App\Models\User;
use App\Models\Klant;
use App\Models\Afspraak;
use App\Models\Behandeling;
use App\Models\Medewerker;
use App\Models\Beschikbaarheid;
use App\Models\MedewerkerPerBehandeling;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

function createTestKlant() {
    $email = 'testclient_' . uniqid() . '@kniplokettiko.nl';
    $user = User::create([
        'name' => 'Test Client',
        'email' => $email,
        'password' => bcrypt('password'),
        'role' => 'klant'
    ]);
    
    // Klant model doesn't have fillable, so set properties manually
    $klant = new Klant();
    $klant->UserId = $user->id;
    $klant->Voornaam = 'Test';
    $klant->Achternaam = 'Client';
    $klant->Relatienummer = 'R' . rand(10000, 99999);
    $klant->IsActief = 1;
    $klant->save();

    return [$user, $klant];
}

test('customer can view their planned appointments', function () {
    list($customerUser, $klant) = createTestKlant();

    $futureDate = Carbon::now()->addDays(10)->toDateString();

    // Create a future appointment
    $afspraak = Afspraak::create([
        'KlantId' => $klant->Id,
        'MedewerkerPerBehandelingId' => 1,
        'BeschikbaarheidId' => 1,
        'Datum' => $futureDate,
        'Starttijd' => '10:00:00',
        'Afspraakstatus' => 'Inbehandeling',
        'IsActief' => DB::raw("b'1'")
    ]);

    $response = $this->actingAs($customerUser)
        ->get(route('afspraken.index'));

    $response->assertStatus(200);
    $response->assertSee('Mijn afspraken');
    $response->assertSee('Geplande afspraken');
    $response->assertSee(Carbon::now()->addDays(10)->format('d-m-Y'));
});

test('customer without planned appointments sees empty message', function () {
    list($customerUser, $klant) = createTestKlant();

    $response = $this->actingAs($customerUser)
        ->get(route('afspraken.index'));

    $response->assertStatus(200);
    $response->assertSee('Je hebt nog geen afspraken.');
});

test('customer can book a new appointment', function () {
    list($customerUser, $klant) = createTestKlant();

    $futureDate = Carbon::now()->addDays(11)->toDateString();
    
    // Set up availability for medewerker 1
    $beschikbaarheid = Beschikbaarheid::create([
        'MedewerkerId' => 1,
        'Dagnaam' => 'Woensdag',
        'Datum' => $futureDate,
        'Starttijd' => '09:00:00',
        'Eindtijd' => '17:00:00',
        'BeschStatus' => 'Beschikbaar',
        'IsActief' => DB::raw("b'1'")
    ]);

    // Ensure medewerker 1 is linked to treatment 1
    MedewerkerPerBehandeling::firstOrCreate([
        'MedewerkerId' => 1,
        'BehandelingId' => 1,
    ], [
        'IsActief' => DB::raw("b'1'")
    ]);

    $postData = [
        'behandeling_id' => 1,
        'medewerker_id' => 1,
        'datum' => $futureDate,
        'starttijd' => '10:00:00',
    ];

    $response = $this->actingAs($customerUser)
        ->post(route('afspraken.store'), $postData);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('afspraken.index'));

    $this->assertDatabaseHas('afspraak', [
        'KlantId' => $klant->Id,
        'Datum' => $futureDate,
        'Starttijd' => '10:00:00',
        'Afspraakstatus' => 'Inbehandeling'
    ]);
});

test('customer cannot book appointment if employee is not available', function () {
    list($customerUser, $klant) = createTestKlant();

    $futureDate = Carbon::now()->addDays(12)->toDateString();

    $postData = [
        'behandeling_id' => 1,
        'medewerker_id' => 1,
        'datum' => $futureDate,
        'starttijd' => '10:00:00',
    ];

    $response = $this->actingAs($customerUser)
        ->from(route('afspraken.create'))
        ->post(route('afspraken.store'), $postData);

    $response->assertRedirect(route('afspraken.create'));
    $response->assertSessionHasErrors(['datum']);
});

test('customer can edit an appointment', function () {
    list($customerUser, $klant) = createTestKlant();

    $futureDate = Carbon::now()->addDays(13)->toDateString();

    // Create availability
    $besch = Beschikbaarheid::create([
        'MedewerkerId' => 1,
        'Dagnaam' => 'Woensdag',
        'Datum' => $futureDate,
        'Starttijd' => '09:00:00',
        'Eindtijd' => '17:00:00',
        'BeschStatus' => 'Beschikbaar',
        'IsActief' => DB::raw("b'1'")
    ]);

    // Create an appointment
    $afspraak = Afspraak::create([
        'KlantId' => $klant->Id,
        'MedewerkerPerBehandelingId' => 1,
        'BeschikbaarheidId' => $besch->Id,
        'Datum' => $futureDate,
        'Starttijd' => '10:00:00',
        'Afspraakstatus' => 'Inbehandeling',
        'IsActief' => DB::raw("b'1'")
    ]);

    $updateData = [
        'behandeling_id' => 1,
        'medewerker_id' => 1,
        'datum' => $futureDate,
        'starttijd' => '11:00:00',
    ];

    $response = $this->actingAs($customerUser)
        ->put(route('afspraken.update', $afspraak->Id), $updateData);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('afspraken.index'));

    $afspraak->refresh();
    expect($afspraak->Starttijd)->toBe('11:00:00');
});

test('customer can cancel a future appointment', function () {
    list($customerUser, $klant) = createTestKlant();

    $futureDate = Carbon::now()->addDays(14)->toDateString();

    // Create a future appointment
    $afspraak = Afspraak::create([
        'KlantId' => $klant->Id,
        'MedewerkerPerBehandelingId' => 1,
        'BeschikbaarheidId' => 1,
        'Datum' => $futureDate,
        'Starttijd' => '12:00:00',
        'Afspraakstatus' => 'Inbehandeling',
        'IsActief' => DB::raw("b'1'")
    ]);

    $response = $this->actingAs($customerUser)
        ->post(route('afspraken.destroy', $afspraak->Id));

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('afspraken.index'));

    $afspraak->refresh();
    expect($afspraak->Afspraakstatus)->toBe('Geannuleerd');
});

test('customer cannot cancel a past appointment', function () {
    list($customerUser, $klant) = createTestKlant();

    $pastDate = Carbon::now()->subDays(5)->toDateString();

    // Create a past appointment
    $afspraak = Afspraak::create([
        'KlantId' => $klant->Id,
        'MedewerkerPerBehandelingId' => 1,
        'BeschikbaarheidId' => 1,
        'Datum' => $pastDate,
        'Starttijd' => '12:00:00',
        'Afspraakstatus' => 'Inbehandeling',
        'IsActief' => DB::raw("b'1'")
    ]);

    $response = $this->actingAs($customerUser)
        ->post(route('afspraken.destroy', $afspraak->Id));

    $response->assertRedirect(route('afspraken.index'));
    $response->assertSessionHas('foutmelding', 'Deze afspraak kan niet meer geannuleerd worden.');

    $afspraak->refresh();
    expect($afspraak->Afspraakstatus)->toBe('Inbehandeling'); // Remains active
});
