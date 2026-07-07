<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Behandeling extends Model
{
    protected $table = 'Behandeling';

    protected $primaryKey = 'Id';

    public $timestamps = false;

    public static function opties(): Collection
    {
        try {
            // Eerst de stored procedure gebruiken; als die ontbreekt valt het model terug op Query Builder.
            return collect(DB::select('CALL sp_behandelingen_opties()'))
                ->pluck('Naam')
                ->prepend('Alle behandelingen')
                ->push('Overig')
                ->values();
        } catch (\Exception) {
            return static::optiesQuery();
        }
    }

    private static function optiesQuery(): Collection
    {
        return DB::table('Behandeling')
            ->where('IsActief', 1)
            ->orderBy('Id')
            ->pluck('Naam')
            ->prepend('Alle behandelingen')
            ->push('Overig')
            ->values();
    }

    public static function overzicht(?string $naam = null): LengthAwarePaginator
    {
        try {
            // Stored procedures leveren gewone arrays terug, daarom pagineren we hier handmatig.
            $behandelingen = collect(DB::select('CALL sp_behandelingen_overzicht(?)', [$naam]));
            $page = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 4;

            return new LengthAwarePaginator(
                $behandelingen->forPage($page, $perPage)->values(),
                $behandelingen->count(),
                $perPage,
                $page,
                [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]
            );
        } catch (\Exception) {
            return static::overzichtQuery($naam);
        }
    }

    private static function overzichtQuery(?string $naam = null): LengthAwarePaginator
    {
        $query = DB::table('Behandeling')
            ->select([
                'Behandeling.Id',
                'Behandeling.Naam',
                'Behandeling.Omschrijving',
                'Behandeling.Duurminuten',
                'Behandeling.Prijs',
                DB::raw('COUNT(DISTINCT Product.Id) as AantalProducten'),
            ])
            ->join('BehandelingPerVoorraad', function ($join): void {
                $join->on('BehandelingPerVoorraad.BehandelingId', '=', 'Behandeling.Id')
                    ->where('BehandelingPerVoorraad.IsActief', 1);
            })
            ->join('Voorraad', function ($join): void {
                $join->on('Voorraad.Id', '=', 'BehandelingPerVoorraad.VoorraadId')
                    ->where('Voorraad.IsActief', 1);
            })
            ->join('Product', function ($join): void {
                $join->on('Product.Id', '=', 'Voorraad.ProductId')
                    ->where('Product.IsActief', 1);
            })
            ->join('MedewerkerPerBehandeling', function ($join): void {
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

        if ($naam !== null && $naam !== '' && $naam !== 'Alle behandelingen') {
            $query->where('Behandeling.Naam', $naam);
        }

        return $query->paginate(4)->withQueryString();
    }

    public static function detail(int $id): object
    {
        try {
            return collect(DB::select('CALL sp_behandeling_detail(?)', [$id]))->firstOrFail();
        } catch (\Exception) {
            return DB::table('Behandeling')
            ->where('Id', $id)
            ->where('IsActief', 1)
            ->firstOrFail();
        }
    }

    public static function producten(int $behandelingId): Collection
    {
        try {
            return collect(DB::select('CALL sp_behandeling_producten(?)', [$behandelingId]));
        } catch (\Exception) {
            // Inner joins tonen alleen producten die echt aan deze actieve behandeling gekoppeld zijn.
            return DB::table('BehandelingPerVoorraad')
            ->join('Behandeling', 'Behandeling.Id', '=', 'BehandelingPerVoorraad.BehandelingId')
            ->join('Voorraad', 'Voorraad.Id', '=', 'BehandelingPerVoorraad.VoorraadId')
            ->join('Product', 'Product.Id', '=', 'Voorraad.ProductId')
            ->where('BehandelingPerVoorraad.BehandelingId', $behandelingId)
            ->where('Behandeling.IsActief', 1)
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
        }
    }

    public static function productDetail(int $behandelingId, int $productId): object
    {
        try {
            return collect(DB::select('CALL sp_behandeling_product_detail(?, ?)', [$behandelingId, $productId]))->firstOrFail();
        } catch (\Exception) {
            return DB::table('BehandelingPerVoorraad')
            ->join('Behandeling', 'Behandeling.Id', '=', 'BehandelingPerVoorraad.BehandelingId')
            ->join('Voorraad', 'Voorraad.Id', '=', 'BehandelingPerVoorraad.VoorraadId')
            ->join('Product', 'Product.Id', '=', 'Voorraad.ProductId')
            ->join('LeverancierOrder', function ($join): void {
                $join->on('LeverancierOrder.ProductId', '=', 'Product.Id')
                    ->where('LeverancierOrder.IsActief', 1);
            })
            ->join('Leverancier', function ($join): void {
                $join->on('Leverancier.Id', '=', 'LeverancierOrder.LeverancierId')
                    ->where('Leverancier.IsActief', 1);
            })
            ->where('BehandelingPerVoorraad.BehandelingId', $behandelingId)
            ->where('Product.Id', $productId)
            ->where('Behandeling.IsActief', 1)
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
    }

    public static function wijzigProductPrijs(int $productId, float $verkoopprijs): void
    {
        try {
            DB::statement('CALL sp_behandeling_product_prijs_bijwerken(?, ?)', [$productId, round($verkoopprijs, 2)]);
        } catch (\Exception) {
            // Fallback wanneer de stored procedure nog niet in de database staat.
            DB::table('Product')
            ->where('Id', $productId)
            ->update([
                'VerkoopPrijs' => round($verkoopprijs, 2),
                'DatumGewijzigd' => now(),
            ]);
        }
    }
}
