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
        $producten = collect(DB::select('CALL sp_producten_overzicht(?)', [$categorieId]));
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
        return collect(DB::select('CALL sp_product_detail(?)', [$id]))->first();
    }

    public static function categorieen(): Collection
    {
        return collect(DB::select('CALL sp_product_categorieen()'));
    }

    public static function wijzigHoudbaarheidsdatum(int $id, string $datum): void
    {
        DB::statement('CALL sp_product_houdbaarheidsdatum_bijwerken(?, ?)', [$id, $datum]);
    }
}
