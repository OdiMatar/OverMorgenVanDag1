<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KlantZoekRequest extends FormRequest
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
            'postcode' => ['nullable', 'string', 'max:10', 'regex:/^[1-9][0-9]{3}\s?[A-Za-z]{2}$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'postcode.regex' => 'Voer een geldige Nederlandse postcode in, bijvoorbeeld 3512AB.',
            'postcode.max' => 'Een postcode mag maximaal 10 tekens bevatten.',
        ];
    }

    public function postcode(): ?string
    {
        $postcode = $this->validated('postcode');

        if (! is_string($postcode) || trim($postcode) === '') {
            return null;
        }

        return strtoupper(str_replace(' ', '', trim($postcode)));
    }
}
