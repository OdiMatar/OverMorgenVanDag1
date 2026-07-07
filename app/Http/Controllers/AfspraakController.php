<?php

namespace App\Http\Controllers;

use App\Models\Afspraak;
use App\Models\Behandeling;
use App\Models\Klant;
use App\Models\Medewerker;
use App\Models\Beschikbaarheid;
use App\Models\MedewerkerPerBehandeling;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AfspraakController extends Controller
{
    private function getKlant()
    {
        $klant = Klant::where('UserId', auth()->id())->first();
        if (!$klant) {
            abort(403, 'U bent geen geregistreerde klant.');
        }
        return $klant;
    }

    public function index()
    {
        $klant = $this->getKlant();

        $vandaag = Carbon::today()->toDateString();
        $nuTijd = Carbon::now()->toTimeString();

        // Active future appointments
        $geplandeAfspraken = Afspraak::where('KlantId', $klant->Id)
            ->whereRaw("IsActief = b'1'")
            ->where('Afspraakstatus', '!=', 'Geannuleerd')
            ->where(function ($query) use ($vandaag, $nuTijd) {
                $query->where('Datum', '>', $vandaag)
                      ->orWhere(function ($q) use ($vandaag, $nuTijd) {
                          $q->where('Datum', '=', $vandaag)
                            ->where('Starttijd', '>=', $nuTijd);
                      });
            })
            ->orderBy('Datum')
            ->orderBy('Starttijd')
            ->get();

        // Historic/cancelled appointments
        $historischeAfspraken = Afspraak::where('KlantId', $klant->Id)
            ->whereRaw("IsActief = b'1'")
            ->where(function ($query) use ($vandaag, $nuTijd) {
                $query->where('Afspraakstatus', '=', 'Geannuleerd')
                      ->orWhere('Datum', '<', $vandaag)
                      ->orWhere(function ($q) use ($vandaag, $nuTijd) {
                          $q->where('Datum', '=', $vandaag)
                            ->where('Starttijd', '<', $nuTijd);
                      });
            })
            ->orderBy('Datum', 'desc')
            ->orderBy('Starttijd', 'desc')
            ->get();

        return view('afspraken.index', compact('geplandeAfspraken', 'historischeAfspraken'));
    }

    public function create()
    {
        $this->getKlant();

        $behandelingen = Behandeling::whereRaw("IsActief = b'1'")->get();
        
        // Retrieve all employees with their specializations
        $medewerkers = Medewerker::whereRaw("IsActief = b'1'")->get();

        // Get unique available dates from beschikbaarheid (only future dates)
        $beschikbareDatums = Beschikbaarheid::whereRaw("IsActief = b'1'")
            ->where('BeschStatus', 'Beschikbaar')
            ->where('Datum', '>=', Carbon::today()->toDateString())
            ->orderBy('Datum')
            ->pluck('Datum')
            ->unique()
            ->values();

        $tijdstippen = [
            '09:00:00', '09:30:00', '10:00:00', '10:30:00', '11:00:00', '11:30:00',
            '12:00:00', '12:30:00', '13:00:00', '13:30:00', '14:00:00', '14:30:00',
            '15:00:00', '15:30:00', '16:00:00', '16:30:00'
        ];

        return view('afspraken.create', compact('behandelingen', 'medewerkers', 'beschikbareDatums', 'tijdstippen'));
    }

    public function store(Request $request)
    {
        $klant = $this->getKlant();

        $request->validate([
            'behandeling_id' => 'required|exists:behandeling,Id',
            'medewerker_id' => 'required|exists:medewerker,Id',
            'datum' => 'required|date|after_or_equal:today',
            'starttijd' => 'required',
        ], [
            'behandeling_id.required' => 'Dit veld is verplicht.',
            'medewerker_id.required' => 'Dit veld is verplicht.',
            'datum.required' => 'Dit veld is verplicht.',
            'starttijd.required' => 'Dit veld is verplicht.',
        ]);

        $behandelingId = $request->input('behandeling_id');
        $medewerkerId = $request->input('medewerker_id');
        $datum = $request->input('datum');
        $starttijd = $request->input('starttijd');

        // Check if employee is linked to the treatment
        $mpb = MedewerkerPerBehandeling::where('MedewerkerId', $medewerkerId)
            ->where('BehandelingId', $behandelingId)
            ->whereRaw("IsActief = b'1'")
            ->first();

        if (!$mpb) {
            Log::info("Booking failed: MPB not found for Medewerker=$medewerkerId Behandeling=$behandelingId");
            return redirect()->back()
                ->withInput()
                ->withErrors(['medewerker_id' => 'Deze medewerker kan deze behandeling niet uitvoeren.']);
        }

        // Check if employee has availability on this date
        $beschikbaarheid = Beschikbaarheid::where('MedewerkerId', $medewerkerId)
            ->where('Datum', $datum)
            ->whereRaw("IsActief = b'1'")
            ->where('BeschStatus', 'Beschikbaar')
            ->first();

        if (!$beschikbaarheid) {
            Log::info("Booking failed: Availability not found for Medewerker=$medewerkerId Datum=$datum");
            return redirect()->back()
                ->withInput()
                ->withErrors(['datum' => 'Deze medewerker is op dit tijdstip niet beschikbaar.']);
        }

        // Check if starttijd is within the availability shift
        if ($starttijd < $beschikbaarheid->Starttijd || $starttijd >= $beschikbaarheid->Eindtijd) {
            Log::info("Booking failed: Starttijd=$starttijd outside shift " . $beschikbaarheid->Starttijd . " - " . $beschikbaarheid->Eindtijd);
            return redirect()->back()
                ->withInput()
                ->withErrors(['starttijd' => 'Deze medewerker is op dit tijdstip niet beschikbaar.']);
        }

        // Check for double booking of employee
        $dubbelGeboekt = Afspraak::where('Datum', $datum)
            ->where('Starttijd', $starttijd)
            ->whereRaw("IsActief = b'1'")
            ->where('Afspraakstatus', '!=', 'Geannuleerd')
            ->whereHas('medewerkerPerBehandeling', function ($query) use ($medewerkerId) {
                $query->where('MedewerkerId', $medewerkerId);
            })
            ->exists();

        if ($dubbelGeboekt) {
            Log::info("Booking failed: Double booked for Medewerker=$medewerkerId Datum=$datum Starttijd=$starttijd");
            return redirect()->back()
                ->withInput()
                ->withErrors(['starttijd' => 'Deze medewerker is op dit tijdstip niet beschikbaar.']);
        }

        Log::info("Booking success: Creating appointment for Klant=" . $klant->Id);

        // Create appointment
        Afspraak::create([
            'KlantId' => $klant->Id,
            'MedewerkerPerBehandelingId' => $mpb->Id,
            'BeschikbaarheidId' => $beschikbaarheid->Id,
            'Datum' => $datum,
            'Starttijd' => $starttijd,
            'Afspraakstatus' => 'Inbehandeling',
            'IsActief' => \Illuminate\Support\Facades\DB::raw("b'1'"),
        ]);

        return redirect()->route('afspraken.index')
            ->with('succesmelding', 'De afspraak is succesvol aangemaakt.');
    }

    public function edit($id)
    {
        $klant = $this->getKlant();

        $afspraak = Afspraak::where('Id', $id)
            ->where('KlantId', $klant->Id)
            ->whereRaw("IsActief = b'1'")
            ->firstOrFail();

        $behandelingen = Behandeling::whereRaw("IsActief = b'1'")->get();
        $medewerkers = Medewerker::whereRaw("IsActief = b'1'")->get();

        $beschikbareDatums = Beschikbaarheid::whereRaw("IsActief = b'1'")
            ->where('BeschStatus', 'Beschikbaar')
            ->where('Datum', '>=', Carbon::today()->toDateString())
            ->orderBy('Datum')
            ->pluck('Datum')
            ->unique()
            ->values();

        $tijdstippen = [
            '09:00:00', '09:30:00', '10:00:00', '10:30:00', '11:00:00', '11:30:00',
            '12:00:00', '12:30:00', '13:00:00', '13:30:00', '14:00:00', '14:30:00',
            '15:00:00', '15:30:00', '16:00:00', '16:30:00'
        ];

        return view('afspraken.edit', compact('afspraak', 'behandelingen', 'medewerkers', 'beschikbareDatums', 'tijdstippen'));
    }

    public function update(Request $request, $id)
    {
        $klant = $this->getKlant();

        $afspraak = Afspraak::where('Id', $id)
            ->where('KlantId', $klant->Id)
            ->whereRaw("IsActief = b'1'")
            ->firstOrFail();

        $request->validate([
            'behandeling_id' => 'required|exists:behandeling,Id',
            'medewerker_id' => 'required|exists:medewerker,Id',
            'datum' => 'required|date|after_or_equal:today',
            'starttijd' => 'required',
        ], [
            'behandeling_id.required' => 'Dit veld is verplicht.',
            'medewerker_id.required' => 'Dit veld is verplicht.',
            'datum.required' => 'Dit veld is verplicht.',
            'starttijd.required' => 'Dit veld is verplicht.',
        ]);

        $behandelingId = $request->input('behandeling_id');
        $medewerkerId = $request->input('medewerker_id');
        $datum = $request->input('datum');
        $starttijd = $request->input('starttijd');

        $mpb = MedewerkerPerBehandeling::where('MedewerkerId', $medewerkerId)
            ->where('BehandelingId', $behandelingId)
            ->whereRaw("IsActief = b'1'")
            ->first();

        if (!$mpb) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['medewerker_id' => 'Deze medewerker kan deze behandeling niet uitvoeren.']);
        }

        $beschikbaarheid = Beschikbaarheid::where('MedewerkerId', $medewerkerId)
            ->where('Datum', $datum)
            ->whereRaw("IsActief = b'1'")
            ->where('BeschStatus', 'Beschikbaar')
            ->first();

        if (!$beschikbaarheid) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['datum' => 'Dit tijdstip is niet beschikbaar.']);
        }

        if ($starttijd < $beschikbaarheid->Starttijd || $starttijd >= $beschikbaarheid->Eindtijd) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['starttijd' => 'Dit tijdstip is niet beschikbaar.']);
        }

        // Check for double booking (excluding this appointment)
        $dubbelGeboekt = Afspraak::where('Id', '!=', $id)
            ->where('Datum', $datum)
            ->where('Starttijd', $starttijd)
            ->whereRaw("IsActief = b'1'")
            ->where('Afspraakstatus', '!=', 'Geannuleerd')
            ->whereHas('medewerkerPerBehandeling', function ($query) use ($medewerkerId) {
                $query->where('MedewerkerId', $medewerkerId);
            })
            ->exists();

        if ($dubbelGeboekt) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['starttijd' => 'Dit tijdstip is niet beschikbaar.']);
        }

        $afspraak->update([
            'MedewerkerPerBehandelingId' => $mpb->Id,
            'BeschikbaarheidId' => $beschikbaarheid->Id,
            'Datum' => $datum,
            'Starttijd' => $starttijd,
        ]);

        return redirect()->route('afspraken.index')
            ->with('succesmelding', 'De afspraak is succesvol gewijzigd.');
    }

    public function destroy($id)
    {
        $klant = $this->getKlant();

        $afspraak = Afspraak::where('Id', $id)
            ->where('KlantId', $klant->Id)
            ->whereRaw("IsActief = b'1'")
            ->firstOrFail();

        $vandaag = Carbon::today()->toDateString();
        $nuTijd = Carbon::now()->toTimeString();

        if ($afspraak->Datum < $vandaag || ($afspraak->Datum === $vandaag && $afspraak->Starttijd < $nuTijd)) {
            return redirect()->route('afspraken.index')
                ->with('foutmelding', 'Deze afspraak kan niet meer geannuleerd worden.');
        }

        $afspraak->update([
            'Afspraakstatus' => 'Geannuleerd',
        ]);

        return redirect()->route('afspraken.index')
            ->with('succesmelding', 'De afspraak is succesvol geannuleerd.');
    }
}
