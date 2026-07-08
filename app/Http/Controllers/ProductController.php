<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        // Leeg of ongeldig betekent: toon alle categorieen.
        $categorieId = $request->integer('categorie_id') ?: null;

        return view('producten.index', [
            'producten' => Product::overzicht($categorieId),
            'categorieen' => Product::categorieen(),
            'geselecteerdeCategorie' => $categorieId,
        ]);
    }

    public function show($id): View
    {
        return view('producten.show', [
            'product' => $this->productOfFail((int) $id),
        ]);
    }

    public function edit($id): View
    {
        return view('producten.edit', [
            'product' => $this->productOfFail((int) $id),
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $product = Product::findOrFail((int) $id);
        $huidigeDatum = Carbon::parse($product->Houdbaarheidsdatum)->startOfDay();
        $nieuweDatumWaarde = $request->input('houdbaarheidsdatum');

        // Een nieuwe houdbaarheidsdatum is verplicht voordat we kunnen vergelijken.
        if (! $nieuweDatumWaarde) {
            return back()
                ->withInput()
                ->with('error', 'Gegevens niet bijgewerkt')
                ->withErrors(['houdbaarheidsdatum' => 'De nieuwe houdbaarheidsdatum is verplicht.']);
        }

        $nieuweDatum = Carbon::parse($nieuweDatumWaarde)->startOfDay();

        // De datum mag niet teruggezet worden.
        if ($nieuweDatum->lt($huidigeDatum)) {
            return back()
                ->withInput()
                ->with('error', 'Gegevens niet bijgewerkt')
                ->withErrors(['houdbaarheidsdatum' => 'De houdbaarheidsdatum mag niet eerder zijn dan de huidige houdbaarheidsdatum.']);
        }

        // De opdrachtregel: verlengen mag maximaal 7 dagen.
        if ($nieuweDatum->gt($huidigeDatum->copy()->addDays(7))) {
            return back()
                ->withInput()
                ->with('error', 'Gegevens niet bijgewerkt')
                ->withErrors(['houdbaarheidsdatum' => 'De houdbaarheidsdatum is met meer dan 7 dagen verlengd.']);
        }

        Product::wijzigHoudbaarheidsdatum($product->Id, $nieuweDatum->format('Y-m-d'));

        return redirect()
            ->route('producten.show', $product->Id)
            ->with('status', 'Houdbaarheidsdatum bijgewerkt.');
    }

    private function productOfFail(int $id): object
    {
        // Product::detail gebruikt de detailquery met categorie, voorraad en leverancier.
        abort_unless($product = Product::detail($id), 404);

        return $product;
    }
}
