<?php

namespace App\Http\Controllers;

use App\Models\Klant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class KlantController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $gevalideerdeGegevens = $request->validate([
            'postcode' => ['nullable', 'string', 'max:10', 'regex:/^[1-9][0-9]{3}\s?[A-Za-z]{2}$/'],
        ], [
            'postcode.regex' => 'Voer een geldige Nederlandse postcode in, bijvoorbeeld 3512AB.',
            'postcode.max' => 'Een postcode mag maximaal 10 tekens bevatten.',
        ]);

        $postcode = $this->normaliseerPostcode($gevalideerdeGegevens['postcode'] ?? null);

        try {
            $klanten = Klant::zoekMetContactgegevens($postcode);
        } catch (Throwable $exception) {
            $this->logTechnischeFout($exception, $postcode);

            return back()
                ->withInput()
                ->with('melding', 'De klantgegevens konden niet worden opgehaald. Probeer het later opnieuw.');
        }

        $melding = null;

        if ($postcode !== null && $klanten->isEmpty()) {
            $melding = 'Er zijn geen klanten bekent die de geselecteerde postcode hebben';
        }

        return view('klanten.index', [
            'klanten' => $klanten,
            'postcode' => $postcode,
            'melding' => $melding,
        ]);
    }

    private function normaliseerPostcode(?string $postcode): ?string
    {
        if ($postcode === null || trim($postcode) === '') {
            return null;
        }

        return strtoupper(str_replace(' ', '', trim($postcode)));
    }

    private function logTechnischeFout(Throwable $exception, ?string $postcode): void
    {
        $context = [
            'postcode' => $postcode,
            'foutmelding' => $exception->getMessage(),
        ];

        // De stored procedure houdt database-logging gelijk aan de technische specificatie.
        try {
            DB::statement('CALL sp_log_technische_melding(?, ?, ?)', [
                'Klantenoverzicht',
                'Klantgegevens ophalen mislukt',
                json_encode($context, JSON_THROW_ON_ERROR),
            ]);
        } catch (Throwable $logException) {
            Log::warning('Technische database-log kon niet worden opgeslagen.', [
                'foutmelding' => $logException->getMessage(),
            ]);
        }

        Log::error('Klantgegevens ophalen mislukt.', $context);
    }
}
