<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Medewerker Model
 * 
 * Vertegenwoordigt een medewerker in het systeem van Kniploket Tiko.
 * Voldoet aan de MVC-architectuur en PSR-12 codeconventies.
 */
class Medewerker extends Model
{
    // Koppel het model expliciet aan de juiste singularis tabelnaam in de database
    protected $table = 'Medewerker';

    // Geef aan dat de primary key met een hoofdletter geschreven is
    protected $primaryKey = 'Id';
    
    // Koppel de aangepaste datumvelden aan de ingebouwde timestamp-functionaliteit van Eloquent
    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // Velden die via mass assignment gevuld mogen worden
    protected $fillable = [
        'UserId',
        'Voornaam',
        'Tussenvoegsel',
        'Achternaam',
        'Specialisatie',
        'Geboortedatum',
        'IsActief',
        'Opmerking',
    ];

    // Zorg ervoor dat Geboortedatum altijd als een Carbon date object wordt behandeld
    protected $casts = [
        'Geboortedatum' => 'date',
    ];

    /**
     * Definieert de veel-op-veel relatie met het Contact model via de pivot-tabel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'MedewerkerPerContact', 'MedewerkerId', 'ContactId');
    }

    /**
     * Helper-methode om het eerste contactadres van de medewerker op te halen.
     *
     * @return Contact|null
     */
    public function getContactAttribute()
    {
        return $this->contacts()->first();
    }

    /**
     * Accessor om de volledige naam van de medewerker samen te stellen.
     * Houdt rekening met eventuele tussenvoegsels.
     *
     * @return string
     */
    public function getNaamAttribute(): string
    {
        return $this->Tussenvoegsel
            ? "{$this->Voornaam} {$this->Tussenvoegsel} {$this->Achternaam}"
            : "{$this->Voornaam} {$this->Achternaam}";
    }

    /**
     * Methode om een volledige naam te parsen en de Voornaam, Tussenvoegsel en Achternaam bij te werken.
     * Verhindert onnodige updates als de naam ongewijzigd is gebleven.
     *
     * @param string $fullName
     * @return void
     */
    public function updateName(string $fullName): void
    {
        if ($this->naam === $fullName) {
            return;
        }
        
        $parts = explode(' ', trim($fullName));
        if (count($parts) === 1) {
            $this->Voornaam = $parts[0];
            $this->Tussenvoegsel = null;
            $this->Achternaam = '';
        } else {
            $this->Voornaam = array_shift($parts);
            $this->Achternaam = array_pop($parts);
            $this->Tussenvoegsel = count($parts) > 0 ? implode(' ', $parts) : null;
        }
    }

    /**
     * Definieert de relatie met de User (accountgegevens).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'UserId', 'id');
    }
}
