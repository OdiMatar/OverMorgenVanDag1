<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BehandelingController extends Controller
{
    public function index(Request $request): View
    {
        $selectedBehandeling = $request->string('behandeling')->trim()->toString();

        $behandelingOpties = DB::table('Behandeling')
            ->where('IsActief', 1)
            ->orderBy('Id')
            ->pluck('Naam')
            ->prepend('Alle behandelingen')
            ->push('Overig')
            ->values();

        $query = DB::table('Behandeling')
            ->select([
                'Behandeling.Id',
                'Behandeling.Naam',
                'Behandeling.Omschrijving',
                'Behandeling.Duurminuten',
                'Behandeling.Prijs',
                DB::raw('COUNT(DISTINCT Product.Id) as AantalProducten'),
            ])
            ->leftJoin('BehandelingPerVoorraad', function ($join): void {
                $join->on('BehandelingPerVoorraad.BehandelingId', '=', 'Behandeling.Id')
                    ->where('BehandelingPerVoorraad.IsActief', 1);
            })
            ->leftJoin('Voorraad', function ($join): void {
                $join->on('Voorraad.Id', '=', 'BehandelingPerVoorraad.VoorraadId')
                    ->where('Voorraad.IsActief', 1);
            })
            ->leftJoin('Product', function ($join): void {
                $join->on('Product.Id', '=', 'Voorraad.ProductId')
                    ->where('Product.IsActief', 1);
            })
            ->leftJoin('MedewerkerPerBehandeling', function ($join): void {
                $join->on('MedewerkerPerBehandeling.BehandelingId', '=', 'Behandeling.Id')
                    ->where('MedewerkerPerBehandeling.IsActief', 1);
            })
            ->where('Behandeling.IsActief', 1)
            ->groupBy([
                'Behandeling.Id',
                'Behandeling.Naam',
                'Behandeling.Omschrijving',
                'Behandeling.Duurminuten',
                'Behandeling.Prijs',
            ])
            ->orderBy('Behandeling.Naam');

        if ($selectedBehandeling !== '' && $selectedBehandeling !== 'Alle behandelingen') {
            $query->where('Behandeling.Naam', $selectedBehandeling);
        }

        $behandelingen = $query
            ->paginate(4)
            ->withQueryString();

        return view('behandelingen.index', [
            'behandelingen' => $behandelingen,
            'behandelingOpties' => $behandelingOpties,
            'selectedBehandeling' => $selectedBehandeling ?: 'Alle behandelingen',
        ]);
    }
}
