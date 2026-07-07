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
        $klanten = DB::select('CALL sp_klant_detail(?)', [$klantId]);

        return $klanten[0] ?? null;
    }

    public static function contactEmailBestaat(string $email, int $klantId): bool
    {
        $resultaat = DB::select('CALL sp_klant_email_bestaat(?, ?)', [$email, $klantId]);

        return (int) ($resultaat[0]->aantal ?? 0) > 0;
    }

    /**
     * @param  array<string, mixed>  $contactgegevens
     */
    public static function werkContactgegevensBij(int $klantId, array $contactgegevens): int
    {
        $resultaat = DB::select('CALL sp_klant_contact_bijwerken(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $klantId,
            $contactgegevens['email'],
            $contactgegevens['mobiel'],
            $contactgegevens['straatnaam'],
            $contactgegevens['huisnummer'],
            $contactgegevens['toevoeging'],
            $contactgegevens['postcode'],
            $contactgegevens['woonplaats'],
            $contactgegevens['bijzonderheden'],
        ]);

        return (int) ($resultaat[0]->aantal_gewijzigd ?? 0);
    }
}
