<?php

namespace App\Http\Controllers;

use App\Http\Requests\KlantBijwerkRequest;
use App\Http\Requests\KlantZoekRequest;
use App\Models\Klant;
use App\Models\TechnicalLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class KlantController extends Controller
{
    public function index(KlantZoekRequest $request): View|RedirectResponse
    {
        $postcode = $request->postcode();

        try {
            $klanten = Klant::zoekMetContactgegevens($postcode);
        } catch (Throwable $exception) {
            $this->logTechnischeFout($exception, 'Klantenoverzicht ophalen', [
                'postcode' => $postcode,
            ]);

            return back()
                ->withInput()
                ->with('melding', 'De klantgegevens konden niet worden opgehaald. Probeer het later opnieuw.');
        }

        $melding = null;

        if ($postcode !== null) {
            $melding = $klanten->isEmpty()
                ? 'Er zijn geen klanten bekent die de geselecteerde postcode hebben'
                : 'Klanten gefilterd op postcode '.$postcode.'.';
        }

        Log::channel('klanten')->info('Klantenoverzicht geopend.', [
            'postcode' => $postcode,
            'aantal_klanten' => $klanten->count(),
            'gebruiker_id' => $request->user()?->id,
        ]);

        return view('klanten.index', [
            'klanten' => $klanten,
            'postcode' => $postcode,
            'melding' => $melding,
        ]);
    }

    public function show(int $klantId): View|RedirectResponse
    {
        try {
            $klant = Klant::vindMetContactgegevens($klantId);
        } catch (Throwable $exception) {
            $this->logTechnischeFout($exception, 'Klantdetails ophalen', [
                'klant_id' => $klantId,
            ]);

            return redirect()
                ->route('klanten.index')
                ->with('melding', 'De klantdetails konden niet worden opgehaald. Probeer het later opnieuw.');
        }

        if ($klant === null) {
            Log::channel('klanten')->warning('Klantdetails niet gevonden.', [
                'klant_id' => $klantId,
            ]);

            return redirect()
                ->route('klanten.index')
                ->with('melding', 'De gekozen klant is niet gevonden.');
        }

        Log::channel('klanten')->info('Klantdetails geopend.', [
            'klant_id' => $klantId,
        ]);

        return view('klanten.show', [
            'klant' => $klant,
        ]);
    }

    public function edit(int $klantId): View|RedirectResponse
    {
        try {
            $klant = Klant::vindMetContactgegevens($klantId);
        } catch (Throwable $exception) {
            $this->logTechnischeFout($exception, 'Klant wijzigen openen', ['klant_id' => $klantId]);

            return redirect()
                ->route('klanten.index')
                ->with('melding', 'De klantgegevens konden niet worden opgehaald. Probeer het later opnieuw.');
        }

        if ($klant === null) {
            Log::channel('klanten')->warning('Klant wijzigen niet gevonden.', [
                'klant_id' => $klantId,
            ]);

            return redirect()
                ->route('klanten.index')
                ->with('melding', 'De gekozen klant is niet gevonden.');
        }

        Log::channel('klanten')->info('Klant wijzigen geopend.', [
            'klant_id' => $klantId,
        ]);

        return view('klanten.edit', [
            'klant' => $klant,
        ]);
    }

    public function update(KlantBijwerkRequest $request, int $klantId): RedirectResponse
    {
        $contactgegevens = $request->contactgegevens();

        try {
            if (Klant::contactEmailBestaat($contactgegevens['email'], $klantId)) {
                Log::channel('klanten')->warning('Klantgegevens zijn niet bijgewerkt: e-mail bestaat al.', [
                    'klant_id' => $klantId,
                    'email' => $contactgegevens['email'],
                ]);

                return back()
                    ->withInput()
                    ->withErrors(['email' => 'Het e-mailadres is al in gebruik'])
                    ->with('foutmelding', 'Klantgegevens zijn niet bijgewerkt');
            }

            $aantalGewijzigd = Klant::werkContactgegevensBij($klantId, $contactgegevens);
        } catch (Throwable $exception) {
            $this->logTechnischeFout($exception, 'Klant wijzigen opslaan', [
                'klant_id' => $klantId,
                'email' => $contactgegevens['email'],
            ]);

            return back()
                ->withInput()
                ->with('foutmelding', 'Klantgegevens zijn niet bijgewerkt');
        }

        if ($aantalGewijzigd === 0) {
            Log::channel('klanten')->info('Klantgegevens ongewijzigd opgeslagen.', [
                'klant_id' => $klantId,
            ]);

            return back()
                ->withInput()
                ->with('foutmelding', 'Klantgegevens zijn niet bijgewerkt');
        }

        Log::channel('klanten')->info('Klantgegevens bijgewerkt.', [
            'klant_id' => $klantId,
            'email' => $contactgegevens['email'],
        ]);

        return redirect()
            ->route('klanten.index')
            ->with('succesmelding', 'Klantgegevens bijgewerkt');
    }

    /**
     * @param  array<string, mixed>  $extraContext
     */
    private function logTechnischeFout(Throwable $exception, string $actie, array $extraContext = []): void
    {
        $context = array_merge([
            'actie' => $actie,
            'foutmelding' => $exception->getMessage(),
        ], $extraContext);

        // De stored procedure houdt database-logging gelijk aan de technische specificatie.
        try {
            TechnicalLog::schrijf('Klanten', $actie.' mislukt', $context);
        } catch (Throwable $logException) {
            Log::warning('Technische database-log kon niet worden opgeslagen.', [
                'foutmelding' => $logException->getMessage(),
            ]);
        }

        Log::channel('klanten')->error('Klantactie mislukt.', $context);
        Log::error('Klantgegevens ophalen mislukt.', $context);
    }
}
