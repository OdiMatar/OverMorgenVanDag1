<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedewerkerPerBehandeling extends Model
{
    protected $table = 'medewerkerperbehandeling';

    protected $primaryKey = 'Id';

    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    protected $fillable = [
        'MedewerkerId',
        'BehandelingId',
        'IsActief',
        'Opmerking',
    ];

    public function medewerker()
    {
        return $this->belongsTo(Medewerker::class, 'MedewerkerId', 'Id');
    }

    public function behandeling()
    {
        return $this->belongsTo(Behandeling::class, 'BehandelingId', 'Id');
    }
}
