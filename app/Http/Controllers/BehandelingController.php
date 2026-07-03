<?php

namespace App\Http\Controllers;

use App\Models\Behandeling;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BehandelingController extends Controller
{
    public function index(Request $request): View
    {
        $selectedBehandeling = $request->string('behandeling')->trim()->toString();

        return view('behandelingen.index', [
            'scherm' => 'overzicht',
            'behandelingen' => Behandeling::overzicht($selectedBehandeling),
            'behandelingOpties' => Behandeling::opties(),
            'selectedBehandeling' => $selectedBehandeling ?: 'Alle behandelingen',
        ]);
    }

    public function producten(int $behandeling): View
    {
        return view('behandelingen.index', [
            'scherm' => 'producten',
            'behandeling' => Behandeling::detail($behandeling),
            'producten' => Behandeling::producten($behandeling),
        ]);
    }

    public function productDetail(int $behandeling, int $product): View
    {
        return view('behandelingen.index', [
            'scherm' => 'detail',
            'behandeling' => Behandeling::detail($behandeling),
            'product' => Behandeling::productDetail($behandeling, $product),
        ]);
    }

    public function productWijzigen(int $behandeling, int $product): View
    {
        return view('behandelingen.index', [
            'scherm' => 'wijzigen',
            'behandeling' => Behandeling::detail($behandeling),
            'product' => Behandeling::productDetail($behandeling, $product),
        ]);
    }

    public function productOpslaan(Request $request, int $behandeling, int $product): RedirectResponse
    {
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
    }

    private function decimalFromInput(string $value): ?float
    {
        $normalized = str_replace(',', '.', trim($value));

        return is_numeric($normalized) ? (float) $normalized : null;
    }
}
