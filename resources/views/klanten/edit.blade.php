@include('components.site-navbar')

<style>
    .klant-edit-page {
        margin-top: 35px;
    }

    .klant-edit-breadcrumb {
        display: flex;
        gap: 8px;
        align-items: center;
        margin: 0 0 16px;
        color: #777f89;
        font-size: 13px;
        font-weight: 700;
    }

    .klant-edit-breadcrumb a {
        color: #d40a2f;
        text-decoration: none;
    }

    .klant-edit-title {
        margin: 0 0 13px;
        color: #d40a2f;
        font-size: 22px;
        line-height: 1.2;
    }

    .klant-edit-title span {
        color: #6f7886;
    }

    .klant-edit-alert {
        width: min(100%, 620px);
        box-sizing: border-box;
        margin: 0 0 18px;
        padding: 14px 16px;
        border-radius: 5px;
        background: #f9d4d9;
        color: #7e2230;
        font-size: 14px;
        font-weight: 700;
    }

    .klant-edit-card {
        width: min(100%, 760px);
        padding: 18px;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 12px 24px rgb(20 25 35 / 0.08);
    }

    .klant-edit-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px 16px;
    }

    .klant-edit-field {
        display: grid;
        gap: 6px;
        color: #263244;
        font-size: 12px;
        font-weight: 800;
    }

    .klant-edit-field span,
    .klant-edit-required span {
        color: #d40a2f;
    }

    .klant-edit-field input {
        width: 100%;
        min-height: 34px;
        box-sizing: border-box;
        padding: 0 9px;
        border: 1px solid #d6dde6;
        border-radius: 7px;
        background: #fff;
        color: #344054;
        font-size: 13px;
    }

    .klant-edit-field input:disabled {
        background: #f7f8fa;
        color: #8490a4;
    }

    .klant-edit-field--wide {
        grid-column: 1 / -1;
    }

    .klant-edit-field input.is-invalid,
    .klant-edit-field input:invalid:not(:placeholder-shown) {
        border-color: #ff4658;
        color: #d40a2f;
    }

    .klant-edit-error {
        color: #ff4658;
        font-size: 12px;
        font-weight: 400;
    }

    .klant-edit-required {
        margin: 16px 0 14px;
        color: #718096;
        font-size: 12px;
    }

    .klant-edit-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .klant-edit-save,
    .klant-edit-back {
        display: inline-flex;
        min-height: 32px;
        align-items: center;
        justify-content: center;
        padding: 0 13px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }

    .klant-edit-save {
        border: 0;
        background: #d40a2f;
        color: #fff;
        cursor: pointer;
    }

    .klant-edit-back {
        background: #737f8d;
        color: #fff;
    }

    @media (max-width: 680px) {
        .klant-edit-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<main class="page-shell klant-edit-page">
    @if (session('foutmelding'))
        <p class="klant-edit-alert" role="alert">{{ session('foutmelding') }}</p>
    @endif

    <nav class="klant-edit-breadcrumb" aria-label="Kruimelpad">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <a href="{{ route('klanten.index') }}">Klanten</a>
        <span>/</span>
        <span>Wijzigen</span>
    </nav>

    <h1 class="klant-edit-title">
        Klant wijzigen <span>{{ trim($klant->voornaam . ' ' . ($klant->tussenvoegsel ? $klant->tussenvoegsel . ' ' : '') . $klant->achternaam) }}</span>
    </h1>

    <form class="klant-edit-card" method="POST" action="{{ route('klanten.update', $klant->klant_id) }}">
        @csrf
        @method('PUT')

        <div class="klant-edit-grid">
            <label class="klant-edit-field">
                Naam <span>*</span>
                <input type="text" value="{{ trim($klant->voornaam . ' ' . ($klant->tussenvoegsel ? $klant->tussenvoegsel . ' ' : '') . $klant->achternaam) }}" disabled>
            </label>

            <label class="klant-edit-field">
                Relatienummer
                <input type="text" value="{{ $klant->relatienummer }}" disabled>
            </label>

            <label class="klant-edit-field">
                Contact e-mail <span>*</span>
                <input
                    class="@error('email') is-invalid @enderror"
                    name="email"
                    type="email"
                    value="{{ old('email', $klant->email) }}"
                    maxlength="100"
                    required
                >
                @error('email')
                    <small class="klant-edit-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="klant-edit-field">
                Account e-mail
                <input type="email" value="{{ $klant->account_email }}" disabled>
            </label>

            <label class="klant-edit-field">
                Straatnaam <span>*</span>
                <input name="straatnaam" type="text" value="{{ old('straatnaam', $klant->straatnaam) }}" maxlength="50" required>
                @error('straatnaam')
                    <small class="klant-edit-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="klant-edit-field">
                Huisnummer <span>*</span>
                <input name="huisnummer" type="number" value="{{ old('huisnummer', $klant->huisnummer) }}" min="1" max="99999" required>
                @error('huisnummer')
                    <small class="klant-edit-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="klant-edit-field">
                Toevoeging
                <input name="toevoeging" type="text" value="{{ old('toevoeging', $klant->toevoeging) }}" maxlength="10">
                @error('toevoeging')
                    <small class="klant-edit-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="klant-edit-field">
                Postcode <span>*</span>
                <input
                    name="postcode"
                    type="text"
                    value="{{ old('postcode', $klant->postcode) }}"
                    maxlength="10"
                    pattern="[1-9][0-9]{3}\s?[A-Za-z]{2}"
                    required
                >
                @error('postcode')
                    <small class="klant-edit-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="klant-edit-field">
                Plaats <span>*</span>
                <input name="woonplaats" type="text" value="{{ old('woonplaats', $klant->woonplaats) }}" maxlength="50" required>
                @error('woonplaats')
                    <small class="klant-edit-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="klant-edit-field">
                Mobiel <span>*</span>
                <input name="mobiel" type="tel" value="{{ old('mobiel', $klant->mobiel) }}" maxlength="20" required>
                @error('mobiel')
                    <small class="klant-edit-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="klant-edit-field klant-edit-field--wide">
                Bijzonderheden
                <input name="bijzonderheden" type="text" value="{{ old('bijzonderheden', $klant->bijzonderheden) }}" maxlength="255">
                @error('bijzonderheden')
                    <small class="klant-edit-error">{{ $message }}</small>
                @enderror
            </label>
        </div>

        <p class="klant-edit-required">Velden met een <span>*</span> zijn verplicht.</p>

        <div class="klant-edit-actions">
            <button class="klant-edit-save" type="submit">Opslaan</button>
            <a class="klant-edit-back" href="{{ route('klanten.show', $klant->klant_id) }}">Terug</a>
        </div>
    </form>
</main>

@include('components.site-footer')
