<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beschikbaarheid extends Model
{
    protected $table = 'beschikbaarheid';

    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    protected $fillable = [
        'MedewerkerId',
        'Dagnaam',
        'Datum',
        'Starttijd',
        'Eindtijd',
        'BeschStatus',
        'IsActief',
        'Opmerking',
    ];

    public function medewerker()
    {
        return $this->belongsTo(Medewerker::class, 'MedewerkerId', 'Id');
    }
}
