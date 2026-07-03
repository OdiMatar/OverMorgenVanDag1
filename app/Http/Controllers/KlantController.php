<?php

namespace App\Http\Controllers;

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
            $this->logTechnischeFout($exception, $postcode);

            return back()
                ->withInput()
                ->with('melding', 'De klantgegevens konden niet worden opgehaald. Probeer het later opnieuw.');
        }

        $melding = null;

        if ($postcode !== null) {
            $melding = $klanten->isEmpty()
                ? 'Er zijn geen klanten bekent die de geselecteerde postcode hebben'
                : 'Klanten gefilterd op postcode ' . $postcode . '.';
        }

        return view('klanten.index', [
            'klanten' => $klanten,
            'postcode' => $postcode,
            'melding' => $melding,
        ]);
    }

    private function logTechnischeFout(Throwable $exception, ?string $postcode): void
    {
        $context = [
            'postcode' => $postcode,
            'foutmelding' => $exception->getMessage(),
        ];

        // De stored procedure houdt database-logging gelijk aan de technische specificatie.
        try {
            TechnicalLog::schrijf('Klantenoverzicht', 'Klantgegevens ophalen mislukt', $context);
        } catch (Throwable $logException) {
            Log::warning('Technische database-log kon niet worden opgeslagen.', [
                'foutmelding' => $logException->getMessage(),
            ]);
        }

        Log::error('Klantgegevens ophalen mislukt.', $context);
    }
}
