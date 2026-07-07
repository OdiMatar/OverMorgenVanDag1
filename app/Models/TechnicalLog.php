<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TechnicalLog extends Model
{
    protected $table = 'TechnicalLog';

    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'Onderdeel',
        'Melding',
        'Context',
    ];

    /**
     * Schrijft een technische melding via de databaseprocedure.
     *
     * @param  array<string, mixed>  $context
     */
    public static function schrijf(string $onderdeel, string $melding, array $context = []): void
    {
        DB::statement('CALL sp_log_technische_melding(?, ?, ?)', [
            $onderdeel,
            $melding,
            json_encode($context, JSON_THROW_ON_ERROR),
        ]);
    }
}
