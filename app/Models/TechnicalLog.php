<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
