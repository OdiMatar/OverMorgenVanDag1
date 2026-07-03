<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function perBehandeling(int $behandeling): View
    {
        $behandelingRecord = $this->findBehandeling($behandeling);

        $producten = DB::table('BehandelingPerVoorraad')
            ->join('Voorraad', 'Voorraad.Id', '=', 'BehandelingPerVoorraad.VoorraadId')
            ->join('Product', 'Product.Id', '=', 'Voorraad.ProductId')
            ->where('BehandelingPerVoorraad.BehandelingId', $behandeling)
            ->where('BehandelingPerVoorraad.IsActief', 1)
            ->where('Voorraad.IsActief', 1)
            ->where('Product.IsActief', 1)
            ->orderBy('Product.Id')
            ->select([
                'Product.Id',
                'Product.Naam',
                'Product.Merk',
                'Product.Omschrijving',
                'Product.EANcode',
                'Product.VerkoopPrijs',
                'Voorraad.AantalOpVoorraad',
            ])
            ->get();

        return view('producten.index', [
            'behandeling' => $behandelingRecord,
            'producten' => $producten,
        ]);
    }

    public function showPerBehandeling(int $behandeling, int $product): View
    {
        return view('producten.show', [
            'behandeling' => $this->findBehandeling($behandeling),
            'product' => $this->findProductForBehandeling($behandeling, $product),
        ]);
    }

    public function editPerBehandeling(int $behandeling, int $product): View
    {
        return view('producten.edit', [
            'behandeling' => $this->findBehandeling($behandeling),
            'product' => $this->findProductForBehandeling($behandeling, $product),
        ]);
    }

    public function updatePerBehandeling(Request $request, int $behandeling, int $product): RedirectResponse
    {
        $productRecord = $this->findProductForBehandeling($behandeling, $product);
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

        DB::table('Product')
            ->where('Id', $product)
            ->update(['VerkoopPrijs' => round((float) $nieuweVerkoopprijs, 2)]);

        return redirect()
            ->route('behandelingen.producten.show', [$behandeling, $product])
            ->with('status_success', 'Productprijs bijgewerkt.');
    }

    private function findBehandeling(int $behandeling): object
    {
        return DB::table('Behandeling')
            ->where('Id', $behandeling)
            ->where('IsActief', 1)
            ->firstOrFail();
    }

    private function findProductForBehandeling(int $behandeling, int $product): object
    {
        return DB::table('BehandelingPerVoorraad')
            ->join('Voorraad', 'Voorraad.Id', '=', 'BehandelingPerVoorraad.VoorraadId')
            ->join('Product', 'Product.Id', '=', 'Voorraad.ProductId')
            ->leftJoin('LeverancierOrder', function ($join): void {
                $join->on('LeverancierOrder.ProductId', '=', 'Product.Id')
                    ->where('LeverancierOrder.IsActief', 1);
            })
            ->leftJoin('Leverancier', function ($join): void {
                $join->on('Leverancier.Id', '=', 'LeverancierOrder.LeverancierId')
                    ->where('Leverancier.IsActief', 1);
            })
            ->where('BehandelingPerVoorraad.BehandelingId', $behandeling)
            ->where('Product.Id', $product)
            ->where('BehandelingPerVoorraad.IsActief', 1)
            ->where('Voorraad.IsActief', 1)
            ->where('Product.IsActief', 1)
            ->orderByDesc('LeverancierOrder.Id')
            ->select([
                'Product.Id',
                'Product.Naam',
                'Product.Merk',
                'Product.Omschrijving',
                'Product.EANcode',
                'Product.Houdbaarheidsdatum',
                'Product.InkoopPrijs',
                'Product.VerkoopPrijs',
                'Product.Opmerking',
                'Voorraad.AantalOpVoorraad',
                'Leverancier.Naam as LeverancierNaam',
                'Leverancier.Postcode as LeverancierPostcode',
                'Leverancier.Plaats as LeverancierPlaats',
                'Leverancier.Email as LeverancierEmail',
                'Leverancier.Mobiel as LeverancierMobiel',
            ])
            ->firstOrFail();
    }

    private function decimalFromInput(string $value): ?float
    {
        $normalized = str_replace(',', '.', trim($value));

        return is_numeric($normalized) ? (float) $normalized : null;
    }
}
