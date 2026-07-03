<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KlantBijwerkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('view-owner-pages') === true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,dns', 'max:100'],
            'mobiel' => ['required', 'string', 'max:20'],
            'straatnaam' => ['required', 'string', 'max:50'],
            'huisnummer' => ['required', 'integer', 'min:1', 'max:99999'],
            'toevoeging' => ['nullable', 'string', 'max:10'],
            'postcode' => ['required', 'string', 'max:10', 'regex:/^[1-9][0-9]{3}\s?[A-Za-z]{2}$/'],
            'woonplaats' => ['required', 'string', 'max:50'],
            'bijzonderheden' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Contact e-mail is verplicht.',
            'email.email' => 'Voer een geldig e-mailadres in.',
            'postcode.regex' => 'Voer een geldige Nederlandse postcode in, bijvoorbeeld 3512AB.',
            'huisnummer.integer' => 'Huisnummer moet een getal zijn.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function contactgegevens(): array
    {
        $gegevens = $this->validated();
        $gegevens['postcode'] = strtoupper(str_replace(' ', '', $gegevens['postcode']));
        $gegevens['toevoeging'] = $gegevens['toevoeging'] ?? '';
        $gegevens['bijzonderheden'] = $gegevens['bijzonderheden'] ?? '';

        return $gegevens;
    }
}
