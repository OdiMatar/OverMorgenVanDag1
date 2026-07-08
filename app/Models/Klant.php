<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
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
        try {
            $klanten = DB::select('CALL sp_get_klanten_met_contactgegevens(?)', [$postcode]);

            return new Collection($klanten);
        } catch (\Exception) {
            return static::zoekMetContactgegevensQuery($postcode);
        }
    }

    public static function vindMetContactgegevens(int $klantId): ?object
    {
        try {
            $klanten = DB::select('CALL sp_klant_detail(?)', [$klantId]);

            return $klanten[0] ?? null;
        } catch (\Exception) {
            return static::vindMetContactgegevensQuery($klantId);
        }
    }

    public static function contactEmailBestaat(string $email, int $klantId): bool
    {
        try {
            $resultaat = DB::select('CALL sp_klant_email_bestaat(?, ?)', [$email, $klantId]);

            return (int) ($resultaat[0]->aantal ?? 0) > 0;
        } catch (\Exception) {
            return DB::table('Contact as c')
                ->join('KlantPerContact as kpc', 'kpc.ContactId', '=', 'c.Id')
                ->whereRaw('LOWER(c.Email) = LOWER(?)', [$email])
                ->where('kpc.KlantId', '<>', $klantId)
                ->where('c.IsActief', 1)
                ->where('kpc.IsActief', 1)
                ->exists();
        }
    }

    /**
     * @param  array<string, mixed>  $contactgegevens
     */
    public static function werkContactgegevensBij(int $klantId, array $contactgegevens): int
    {
        try {
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
        } catch (\Exception) {
            return DB::table('Contact as c')
                ->join('KlantPerContact as kpc', 'kpc.ContactId', '=', 'c.Id')
                ->join('Klant as k', 'k.Id', '=', 'kpc.KlantId')
                ->where('k.Id', $klantId)
                ->where('k.IsActief', 1)
                ->where('kpc.IsActief', 1)
                ->where('c.IsActief', 1)
                ->update([
                    'c.Email' => $contactgegevens['email'],
                    'c.Mobiel' => $contactgegevens['mobiel'],
                    'c.Straatnaam' => $contactgegevens['straatnaam'],
                    'c.Huisnummer' => $contactgegevens['huisnummer'],
                    'c.Toevoeging' => $contactgegevens['toevoeging'] ?: null,
                    'c.Postcode' => strtoupper(str_replace(' ', '', $contactgegevens['postcode'])),
                    'c.Plaats' => $contactgegevens['woonplaats'],
                    'c.DatumGewijzigd' => now(),
                    'k.Bijzonderheden' => $contactgegevens['bijzonderheden'] ?: null,
                    'k.DatumGewijzigd' => now(),
                ]);
        }
    }

    private static function klantContactQuery()
    {
        return DB::table('Klant as k')
            ->join('KlantPerContact as kpc', 'kpc.KlantId', '=', 'k.Id')
            ->join('Contact as c', 'c.Id', '=', 'kpc.ContactId')
            ->where('k.IsActief', 1)
            ->where('kpc.IsActief', 1)
            ->where('c.IsActief', 1);
    }

    private static function zoekMetContactgegevensQuery(?string $postcode = null): Collection
    {
        $query = static::klantContactQuery()
            ->select([
                'k.Id as klant_id',
                'k.Voornaam as voornaam',
                'k.Tussenvoegsel as tussenvoegsel',
                'k.Achternaam as achternaam',
                'k.Relatienummer as relatienummer',
                DB::raw("CONCAT(c.Straatnaam, ' ', c.Huisnummer, IFNULL(CONCAT(' ', c.Toevoeging), '')) as adres"),
                'c.Postcode as postcode',
                'c.Plaats as woonplaats',
                'c.Mobiel as mobiel',
                'c.Email as email',
            ]);

        if ($postcode !== null && $postcode !== '') {
            $query->whereRaw("REPLACE(UPPER(c.Postcode), ' ', '') = REPLACE(UPPER(?), ' ', '')", [$postcode]);
        }

        return $query
            ->orderBy('k.Achternaam')
            ->orderBy('k.Voornaam')
            ->get();
    }

    private static function vindMetContactgegevensQuery(int $klantId): ?object
    {
        return static::klantContactQuery()
            ->join('users as u', 'u.id', '=', 'k.UserId')
            ->where('k.Id', $klantId)
            ->select([
                'k.Id as klant_id',
                'k.Voornaam as voornaam',
                'k.Tussenvoegsel as tussenvoegsel',
                'k.Achternaam as achternaam',
                'k.Relatienummer as relatienummer',
                'k.Bijzonderheden as bijzonderheden',
                'u.email as account_email',
                'c.Id as contact_id',
                'c.Straatnaam as straatnaam',
                'c.Huisnummer as huisnummer',
                'c.Toevoeging as toevoeging',
                'c.Postcode as postcode',
                'c.Plaats as woonplaats',
                'c.Mobiel as mobiel',
                'c.Email as email',
                DB::raw("CONCAT(c.Straatnaam, ' ', c.Huisnummer, IFNULL(CONCAT(' ', c.Toevoeging), '')) as adres"),
            ])
            ->first();
    }
}
