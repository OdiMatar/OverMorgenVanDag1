<?php

namespace App\Http\Controllers;

use App\Models\Medewerker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * MedewerkerController
 * 
 * Beheert de overzichten, details en wijzigingen van medewerkers binnen Kniploket Tiko.
 * Voldoet aan de MVC-architectuur en PSR-12 codeconventies.
 */
class MedewerkerController extends Controller
{
    /**
     * Toont het overzicht van alle medewerkers, eventueel gefilterd op specialisatie.
     * Maakt gebruik van een Stored Procedure (sp_GetMedewerkersBySpecialisatie) en Joins.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Haal de gekozen specialisatie op uit het filterformulier
        $specialisatie = $request->input('specialisatie');
        
        try {
            // Log de actie voor de technische log
            Log::info("Ophalen van medewerkersoverzicht. Geselecteerde filter: " . ($specialisatie ?: 'geen'));

            // Stored Procedure aanroep met JOINs (eis 2 en 5)
            $results = DB::select('CALL sp_GetMedewerkersBySpecialisatie(?)', [$specialisatie]);

            // Hydrateer de ruwe database-objecten naar Medewerker-modellen zodat Eloquent-relaties werken
            $medewerkerModels = Medewerker::hydrate($results);
            
            // Handmatige paginering (4 medewerkers per pagina) conform de wireframes
            $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
            $perPage = 4;
            $currentItems = $medewerkerModels->slice(($currentPage - 1) * $perPage, $perPage)->all();
            
            $medewerkers = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $medewerkerModels->count(),
                $perPage,
                $currentPage,
                ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
            );
            $medewerkers->withQueryString();

            // Totaal aantal gevonden medewerkers vóór paginering
            $totalFound = $medewerkerModels->count();

        } catch (\Exception $e) {
            // Log de uitzondering in de technische log (eis 11)
            Log::error("Fout bij ophalen medewerkersoverzicht: " . $e->getMessage());
            
            // Val terug op een lege paginator bij fouten om crashen te voorkomen
            $medewerkers = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 4);
            $totalFound = 0;
        }

        // Beschikbare specialisaties voor het dropdown-filter
        $specialisaties = ['Extensions', 'Kleuren', 'Knippen', 'Permanent', 'Stylen'];
        
        // Render de view met de benodigde data (MVC - View component)
        return view('medewerkers.index', compact('medewerkers', 'specialisaties', 'specialisatie', 'totalFound'));
    }

    /**
     * Toont de detailpagina van een specifieke medewerker.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Zoek de medewerker op ID of geef een 404 error
        $medewerker = Medewerker::findOrFail($id);
        
        // Log de technische actie
        Log::info("Medewerkerdetails bekeken voor ID: " . $id);

        return view('medewerkers.show', compact('medewerker'));
    }

    /**
     * Toont het bewerkingsformulier van een medewerker.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $medewerker = Medewerker::findOrFail($id);
        
        // Log de technische actie
        Log::info("Bewerkingspagina geopend voor medewerker ID: " . $id);

        $specialisaties = ['Extensions', 'Kleuren', 'Knippen', 'Permanent', 'Stylen'];
        return view('medewerkers.edit', compact('medewerker', 'specialisaties'));
    }

    /**
     * Slaat de gewijzigde gegevens op in de database.
     * Maakt gebruik van server-side validatie, transacties en een Stored Procedure.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Zoek de medewerker op of gooi een 404
        $medewerker = Medewerker::findOrFail($id);
        
        // Server-side validatie (eis 9)
        $request->validate([
            'naam' => 'required|string|max:255',
            'specialisatie' => 'required|string|in:Extensions,Kleuren,Knippen,Permanent,Stylen',
            'geboortedatum' => 'required|string',
            'email' => 'required|email|max:255',
            'straatnaam' => 'required|string|max:150',
            'huisnummer' => 'required|string|max:20',
            'toevoeging' => 'nullable|string|max:20',
            'postcode' => 'required|string|max:10',
            'plaats' => 'required|string|max:100',
            'mobiel' => 'required|string|max:30',
            'opmerking' => 'nullable|string|max:255',
        ]);

        $dobInput = $request->input('geboortedatum');
        try {
            // Probeer de geboortedatum te parsen in d-m-Y of Y-m-d formaat (eis 3)
            if (Carbon::hasFormat($dobInput, 'd-m-Y')) {
                $birthDate = Carbon::createFromFormat('d-m-Y', $dobInput);
            } else {
                $birthDate = Carbon::parse($dobInput);
            }
        } catch (\Exception $e) {
            // Log de technische fout in de logbestanden (eis 11)
            Log::error("Validatiefout: Ongeldig geboortedatumformaat ingevoerd voor medewerker ID $id: $dobInput. Fout: " . $e->getMessage());
            
            // Terugkoppeling met foutmelding (eis 12)
            session()->flash('error', 'Medewerkergegevens zijn niet bijgewerkt.');
            return back()->withInput()->withErrors(['geboortedatum' => 'De geboortedatum is geen geldige datum.']);
        }

        // Bereken de leeftijd van de medewerker
        $age = $birthDate->diffInYears(Carbon::now());
        
        // Business Rule check: minderjarige medewerkers mogen geen 'Permanent' specialisatie krijgen (eis 9)
        if ($age < 18 && $request->input('specialisatie') === 'Permanent') {
            Log::warning("Validatiefout: Minderjarige medewerker (ID $id, leeftijd $age) mag geen specialisatie Permanent krijgen.");
            
            session()->flash('error', 'Medewerkergegevens zijn niet bijgewerkt.');
            return back()->withInput()->withErrors([
                'specialisatie' => 'Minderjarige medewerkers mogen geen specialisatie Permanent toegewezen krijgen vanwege het werken met gevaarlijke stoffen en chemicaliën.'
            ]);
        }

        // Splits de volledige naam in Voornaam, Tussenvoegsel en Achternaam voor de database
        $fullName = $request->input('naam');
        $parts = explode(' ', trim($fullName));
        if (count($parts) === 1) {
            $voornaam = $parts[0];
            $tussenvoegsel = null;
            $achternaam = '';
        } else {
            $voornaam = array_shift($parts);
            $achternaam = array_pop($parts);
            $tussenvoegsel = count($parts) > 0 ? implode(' ', $parts) : null;
        }

        // Database-transactie en Stored Procedure aanroep met Try Catch (eis 3, 5 en 11)
        try {
            DB::beginTransaction();

            Log::info("Start Stored Procedure sp_UpdateMedewerker voor medewerker ID $id.");

            // Roep de Stored Procedure aan om de medewerker- en contactgegevens in één keer bij te werken
            DB::statement('CALL sp_UpdateMedewerker(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $medewerker->Id,
                $voornaam,
                $tussenvoegsel,
                $achternaam,
                $request->input('specialisatie'),
                $birthDate->format('Y-m-d'),
                $request->input('opmerking'),
                $request->input('straatnaam'),
                $request->input('huisnummer'),
                $request->input('toevoeging'),
                $request->input('postcode'),
                $request->input('plaats'),
                $request->input('email'),
                $request->input('mobiel')
            ]);

            DB::commit();

            // Technische log van succesvolle actie (eis 11)
            Log::info("Medewerkergegevens succesvol bijgewerkt via Stored Procedure voor ID: " . $medewerker->Id);

            // Terugkoppeling acties d.m.v. melding aan eindgebruiker (eis 12)
            return redirect()->route('medewerkers.show', $medewerker->Id)
                ->with('success', 'Medewerkergegevens bijgewerkt.');

        } catch (\Exception $e) {
            // Draai de transactie terug bij een fout
            DB::rollBack();

            // Technische log van de database fout (eis 11)
            Log::error("Databasefout bij bijwerken medewerkergegevens ID $id: " . $e->getMessage());

            // Foutmelding aan eindgebruiker (eis 12)
            session()->flash('error', 'Medewerkergegevens zijn niet bijgewerkt.');
            return back()->withInput()->withErrors(['error' => 'Er is een fout opgetreden in de database bij het opslaan van de gegevens.']);
        }
    }
}
