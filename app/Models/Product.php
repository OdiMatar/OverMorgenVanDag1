<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class Product extends Model
{
    protected $table = 'Product';

    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'Houdbaarheidsdatum',
        'DatumGewijzigd',
    ];

    public static function overzicht(?int $categorieId = null): LengthAwarePaginator
    {
        $producten = collect(static::productenOverzichtData($categorieId));
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 4;

        return new LengthAwarePaginator(
            $producten->forPage($page, $perPage)->values(),
            $producten->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    public static function detail(int $id): ?object
    {
        try {
            return collect(DB::select('CALL sp_product_detail(?)', [$id]))->first();
        } catch (\Exception) {
            return DB::table('Product')
                ->join('Categorie', 'Product.CategorieId', '=', 'Categorie.Id')
                ->leftJoin('Voorraad', 'Product.Id', '=', 'Voorraad.ProductId')
                ->leftJoin(DB::raw('(SELECT ProductId, MIN(Id) AS EersteLeverancierOrderId FROM LeverancierOrder GROUP BY ProductId) AS EersteOrder'), 'Product.Id', '=', 'EersteOrder.ProductId')
                ->leftJoin('LeverancierOrder', 'EersteOrder.EersteLeverancierOrderId', '=', 'LeverancierOrder.Id')
                ->leftJoin('Leverancier', 'LeverancierOrder.LeverancierId', '=', 'Leverancier.Id')
                ->select(
                    'Product.Id',
                    'Product.Naam',
                    'Product.Omschrijving',
                    'Product.Merk',
                    'Product.EANcode',
                    'Product.Houdbaarheidsdatum',
                    'Product.InkoopPrijs',
                    'Product.VerkoopPrijs',
                    'Product.Opmerking',
                    DB::raw('Categorie.Naam AS CategorieNaam'),
                    'Voorraad.AantalOpVoorraad',
                    DB::raw('Leverancier.Naam AS LeverancierNaam'),
                    DB::raw('Leverancier.Postcode AS LeverancierPostcode'),
                    DB::raw('Leverancier.Plaats AS LeverancierPlaats'),
                    DB::raw('Leverancier.Email AS LeverancierEmail'),
                    DB::raw('Leverancier.Mobiel AS LeverancierMobiel')
                )
                ->where('Product.Id', $id)
                ->first();
        }
    }

    public static function categorieen(): Collection
    {
        try {
            return collect(DB::select('CALL sp_product_categorieen()'));
        } catch (\Exception) {
            return DB::table('Categorie')
                ->select('Id', 'Naam')
                ->whereRaw("IsActief = b'1'")
                ->orderBy('Naam')
                ->get();
        }
    }

    public static function wijzigHoudbaarheidsdatum(int $id, string $datum): void
    {
        try {
            DB::statement('CALL sp_product_houdbaarheidsdatum_bijwerken(?, ?)', [$id, $datum]);
        } catch (\Exception) {
            DB::table('Product')
                ->where('Id', $id)
                ->update([
                    'Houdbaarheidsdatum' => $datum,
                    'DatumGewijzigd' => now(),
                ]);
        }
    }

    private static function productenOverzichtData(?int $categorieId): array
    {
        try {
            return DB::select('CALL sp_producten_overzicht(?)', [$categorieId]);
        } catch (\Exception) {
            return DB::table('Product')
                ->join('Categorie', 'Product.CategorieId', '=', 'Categorie.Id')
                ->leftJoin('Voorraad', 'Product.Id', '=', 'Voorraad.ProductId')
                ->select(
                    'Product.Id',
                    'Product.Naam',
                    'Product.Merk',
                    'Product.EANcode',
                    'Product.VerkoopPrijs',
                    DB::raw('Categorie.Naam AS CategorieNaam'),
                    'Voorraad.AantalOpVoorraad'
                )
                ->whereRaw("Product.IsActief = b'1'")
                ->when($categorieId, fn ($query) => $query->where('Product.CategorieId', $categorieId))
                ->orderBy('Categorie.Naam')
                ->orderBy('Product.Naam')
                ->get()
                ->all();
        }
    }
}
