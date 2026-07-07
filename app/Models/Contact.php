<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Contact Model
 * 
 * Beheert de contactgegevens (adres, telefoonnummer, e-mail) van een persoon (klant of medewerker).
 * Voldoet aan de MVC-architectuur en PSR-12 codeconventies.
 */
class Contact extends Model
{
    // Koppel het model expliciet aan de tabelnaam Contact
    protected $table = 'Contact';

    // Primary key van de tabel
    protected $primaryKey = 'Id';
    
    // Koppel de aangepaste datumvelden aan de ingebouwde timestamp-functionaliteit van Eloquent
    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // Velden die via mass assignment gevuld mogen worden
    protected $fillable = [
        'Straatnaam',
        'Huisnummer',
        'Toevoeging',
        'Postcode',
        'Plaats',
        'Email',
        'Mobiel',
        'IsActief',
        'Opmerking',
    ];
}
