@include('components.site-navbar')

<style>
    .afspraak-form-page {
        margin-top: 35px;
    }

    .afspraak-form-breadcrumb {
        display: flex;
        gap: 8px;
        align-items: center;
        margin: 0 0 16px;
        color: #777f89;
        font-size: 13px;
        font-weight: 700;
    }

    .afspraak-form-breadcrumb a {
        color: #d40a2f;
        text-decoration: none;
    }

    .afspraak-form-heading h1 {
        margin: 0 0 12px;
        color: #d40a2f;
        font-size: 22px;
        line-height: 1.2;
    }

    .afspraak-form-card {
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 22px 35px rgb(35 45 65 / 0.08);
        width: min(100%, 640px);
        padding: 24px;
        box-sizing: border-box;
    }

    .afspraak-form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .afspraak-form-grid label {
        display: grid;
        gap: 6px;
        color: #263244;
        font-size: 13px;
        font-weight: 800;
    }

    .afspraak-form-grid label span {
        color: #d40a2f;
    }

    .afspraak-form-grid select,
    .afspraak-form-grid input {
        width: 100%;
        min-height: 38px;
        box-sizing: border-box;
        padding: 0 10px;
        border: 1px solid #d6dde6;
        border-radius: 7px;
        background: #fff;
        color: #344054;
        font-size: 14px;
    }

    .afspraak-form-grid select.is-invalid,
    .afspraak-form-grid input.is-invalid {
        border-color: #ff4658;
        color: #d40a2f;
    }

    .afspraak-field-error {
        color: #ff4658;
        font-size: 12px;
        font-weight: 700;
        margin-top: 4px;
    }

    .afspraak-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 24px;
    }

    .afspraak-btn-submit {
        display: inline-flex;
        min-height: 36px;
        align-items: center;
        justify-content: center;
        padding: 0 18px;
        border-radius: 7px;
        border: 0;
        background: #d40a2f;
        color: #fff;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(212, 10, 47, 0.2);
    }

    .afspraak-btn-submit:hover {
        background: #b30825;
    }

    .afspraak-btn-cancel {
        display: inline-flex;
        min-height: 36px;
        align-items: center;
        justify-content: center;
        padding: 0 18px;
        border-radius: 7px;
        border: 1px solid #737f8d;
        background: #737f8d;
        color: #fff;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
    }

    .afspraak-btn-cancel:hover {
        background: #5a6470;
    }
</style>

<main class="page-shell afspraak-form-page">
    <nav class="afspraak-form-breadcrumb" aria-label="Kruimelpad">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <a href="{{ route('afspraken.index') }}">Mijn afspraken</a>
        <span>/</span>
        <span>Afspraak inplannen</span>
    </nav>

    <div class="afspraak-form-heading">
        <h1>Nieuwe afspraak inplannen</h1>
    </div>

    <section class="afspraak-form-card" aria-label="Afspraak inplannen">
        <form method="POST" action="{{ route('afspraken.store') }}">
            @csrf

            <div class="afspraak-form-grid">
                <!-- 1. Behandeling kiezen -->
                <label for="behandeling_id">
                    Kies een behandeling <span>*</span>
                    <select id="behandeling_id" name="behandeling_id" class="{{ $errors->has('behandeling_id') ? 'is-invalid' : '' }}" required>
                        <option value="">-- Selecteer behandeling --</option>
                        @foreach ($behandelingen as $behandeling)
                            <option value="{{ $behandeling->Id }}" {{ old('behandeling_id') == $behandeling->Id ? 'selected' : '' }}>
                                {{ $behandeling->Naam }} - &euro;{{ number_format($behandeling->Prijs, 2, ',', '.') }} ({{ $behandeling->Duurminuten }} min)
                            </option>
                        @endforeach
                    </select>
                    @error('behandeling_id')
                        <span class="afspraak-field-error">{{ $message }}</span>
                    @enderror
                </label>

                <!-- 2. Medewerker kiezen (Bug US4 Fix: Specialisatie tonen) -->
                <label for="medewerker_id">
                    Kies een medewerker <span>*</span>
                    <select id="medewerker_id" name="medewerker_id" class="{{ $errors->has('medewerker_id') ? 'is-invalid' : '' }}" required>
                        <option value="">-- Selecteer medewerker --</option>
                        @foreach ($medewerkers as $medewerker)
                            <option value="{{ $medewerker->Id }}" {{ old('medewerker_id') == $medewerker->Id ? 'selected' : '' }}>
                                {{ $medewerker->Voornaam }} {{ $medewerker->Achternaam }} (Specialisatie: {{ $medewerker->Specialisatie }})
                            </option>
                        @endforeach
                    </select>
                    @error('medewerker_id')
                        <span class="afspraak-field-error">{{ $message }}</span>
                    @enderror
                </label>

                <!-- 3. Datum kiezen -->
                <label for="datum">
                    Kies een datum <span>*</span>
                    <select id="datum" name="datum" class="{{ $errors->has('datum') ? 'is-invalid' : '' }}" required>
                        <option value="">-- Selecteer datum --</option>
                        @foreach ($beschikbareDatums as $datum)
                            <option value="{{ $datum }}" {{ old('datum') == $datum ? 'selected' : '' }}>
                                {{ Carbon\Carbon::parse($datum)->format('d-m-Y') }}
                            </option>
                        @endforeach
                    </select>
                    @error('datum')
                        <span class="afspraak-field-error">{{ $message }}</span>
                    @enderror
                </label>

                <!-- 4. Tijd kiezen -->
                <label for="starttijd">
                    Kies een starttijd <span>*</span>
                    <select id="starttijd" name="starttijd" class="{{ $errors->has('starttijd') ? 'is-invalid' : '' }}" required>
                        <option value="">-- Selecteer starttijd --</option>
                        @foreach ($tijdstippen as $tijd)
                            <option value="{{ $tijd }}" {{ old('starttijd') == $tijd ? 'selected' : '' }}>
                                {{ Carbon\Carbon::parse($tijd)->format('H:i') }}
                            </option>
                        @endforeach
                    </select>
                    @error('starttijd')
                        <span class="afspraak-field-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <div class="afspraak-actions">
                <a href="{{ route('afspraken.index') }}" class="afspraak-btn-cancel">Annuleren</a>
                <button type="submit" class="afspraak-btn-submit">Afspraak bevestigen</button>
            </div>
        </form>
    </section>
</main>

@include('components.site-footer')
