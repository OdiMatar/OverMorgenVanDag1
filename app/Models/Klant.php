<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Klant extends Model
{
    protected $table = 'Klant';

    protected $primaryKey = 'Id';

    public $timestamps = false;

    /**
     * Haalt actieve klanten met hun gekoppelde contactgegevens op via de databaseprocedure.
     */
    public static function zoekMetContactgegevens(?string $postcode = null): Collection
    {
        $klanten = DB::select('CALL sp_get_klanten_met_contactgegevens(?)', [$postcode]);

        return new Collection($klanten);
    }
}
