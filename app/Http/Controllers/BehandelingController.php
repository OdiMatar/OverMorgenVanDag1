<?php

namespace App\Http\Controllers;

use App\Models\Behandeling;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class BehandelingController extends Controller
{
    public function index(Request $request): View
    {
        try {
            $selectedBehandeling = $request->string('behandeling')->trim()->toString();

            // Haal het overzicht en de filteropties op voor de hoofdpagina.
            return view('behandelingen.index', [
                'scherm' => 'overzicht',
                'behandelingen' => Behandeling::overzicht($selectedBehandeling),
                'behandelingOpties' => Behandeling::opties(),
                'selectedBehandeling' => $selectedBehandeling ?: 'Alle behandelingen',
            ]);
        } catch (Throwable) {
            abort(500, 'De behandelingen kunnen niet worden geladen.');
        }
    }

    public function producten(int $behandeling): View
    {
        try {
            return view('behandelingen.index', [
                'scherm' => 'producten',
                'behandeling' => Behandeling::detail($behandeling),
                'producten' => Behandeling::producten($behandeling),
            ]);
        } catch (Throwable) {
            abort(404, 'Deze behandeling is niet gevonden.');
        }
    }

    public function productDetail(int $behandeling, int $product): View
    {
        try {
            return view('behandelingen.index', [
                'scherm' => 'detail',
                'behandeling' => Behandeling::detail($behandeling),
                'product' => Behandeling::productDetail($behandeling, $product),
            ]);
        } catch (Throwable) {
            abort(404, 'Dit product hoort niet bij deze behandeling.');
        }
    }

    public function productWijzigen(int $behandeling, int $product): View
    {
        try {
            return view('behandelingen.index', [
                'scherm' => 'wijzigen',
                'behandeling' => Behandeling::detail($behandeling),
                'product' => Behandeling::productDetail($behandeling, $product),
            ]);
        } catch (Throwable) {
            abort(404, 'Dit product kan niet worden gewijzigd.');
        }
    }

    public function productOpslaan(Request $request, int $behandeling, int $product): RedirectResponse
    {
        try {
            $productRecord = Behandeling::productDetail($behandeling, $product);
            $nieuweVerkoopprijs = $this->decimalFromInput((string) $request->input('nieuwe_verkoopprijs', ''));
            $minimaleVerkoopprijs = round((float) $productRecord->InkoopPrijs * 1.3, 2);

            $validator = Validator::make(
                ['nieuwe_verkoopprijs' => $nieuweVerkoopprijs],
                ['nieuwe_verkoopprijs' => ['required', 'numeric']],
                [
                    'nieuwe_verkoopprijs.required' => 'Verkoopprijs moet minimaal 30 procent boven de inkoopprijs liggen.',
                    'nieuwe_verkoopprijs.numeric' => 'Verkoopprijs moet minimaal 30 procent boven de inkoopprijs liggen.',
                ],
            );

            // Extra controle: verkoopprijs moet minimaal 30 procent boven de inkoopprijs liggen.
            $validator->after(function ($validator) use ($nieuweVerkoopprijs, $minimaleVerkoopprijs): void {
                if (is_numeric($nieuweVerkoopprijs) && (float) $nieuweVerkoopprijs < $minimaleVerkoopprijs) {
                    $validator->errors()->add('nieuwe_verkoopprijs', 'Verkoopprijs moet minimaal 30 procent boven de inkoopprijs liggen.');
                }
            });

            if ($validator->fails()) {
                return redirect()
                    ->route('behandelingen.producten.edit', [$behandeling, $product])
                    ->withErrors($validator)
                    ->withInput()
                    ->with('status_error', 'Gegevens niet bijgewerkt');
            }

            Behandeling::wijzigProductPrijs($product, (float) $nieuweVerkoopprijs);

            return redirect()
                ->route('behandelingen.producten.show', [$behandeling, $product])
                ->with('status_success', 'Productprijs bijgewerkt.');
        } catch (Throwable) {
            return redirect()
                ->route('behandelingen.producten.edit', [$behandeling, $product])
                ->withInput()
                ->with('status_error', 'Productprijs kon niet worden bijgewerkt.');
        }
    }

    private function decimalFromInput(string $value): ?float
    {
        $normalized = str_replace(',', '.', trim($value));

        return is_numeric($normalized) ? (float) $normalized : null;
    }
}
