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

    public static function vindMetContactgegevens(int $klantId): ?object
    {
        $klanten = DB::select(
            "SELECT
                k.Id AS klant_id,
                k.Voornaam AS voornaam,
                k.Tussenvoegsel AS tussenvoegsel,
                k.Achternaam AS achternaam,
                k.Relatienummer AS relatienummer,
                k.Bijzonderheden AS bijzonderheden,
                CONCAT(c.Straatnaam, ' ', c.Huisnummer, IFNULL(CONCAT(' ', c.Toevoeging), '')) AS adres,
                c.Postcode AS postcode,
                c.Plaats AS woonplaats,
                c.Mobiel AS mobiel,
                c.Email AS email
            FROM Klant AS k
            INNER JOIN KlantPerContact AS kpc ON kpc.KlantId = k.Id
            INNER JOIN Contact AS c ON c.Id = kpc.ContactId
            WHERE k.Id = ?
                AND k.IsActief = b'1'
                AND kpc.IsActief = b'1'
                AND c.IsActief = b'1'
            LIMIT 1",
            [$klantId]
        );

        return $klanten[0] ?? null;
    }
}
