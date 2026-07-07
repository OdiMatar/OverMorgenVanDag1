<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Afspraak extends Model
{
    protected $table = 'afspraak';

    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    protected $fillable = [
        'KlantId',
        'MedewerkerPerBehandelingId',
        'BeschikbaarheidId',
        'Datum',
        'Starttijd',
        'Afspraakstatus',
        'IsActief',
        'Opmerking',
    ];

    public function klant()
    {
        return $this->belongsTo(Klant::class, 'KlantId', 'Id');
    }

    public function medewerkerPerBehandeling()
    {
        return $this->belongsTo(MedewerkerPerBehandeling::class, 'MedewerkerPerBehandelingId', 'Id');
    }

    public function beschikbaarheid()
    {
        return $this->belongsTo(Beschikbaarheid::class, 'BeschikbaarheidId', 'Id');
    }
}
