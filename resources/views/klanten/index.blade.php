@include('components.site-navbar')

<style>
    .klanten-page {
        margin-top: 35px;
    }

    .klanten-breadcrumb {
        display: flex;
        gap: 8px;
        align-items: center;
        margin: 0 0 16px;
        color: #777f89;
        font-size: 13px;
        font-weight: 700;
    }

    .klanten-breadcrumb a {
        color: #d40a2f;
        text-decoration: none;
    }

    .klanten-heading h1 {
        margin: 0 0 12px;
        color: #d40a2f;
        font-size: 22px;
        line-height: 1.2;
    }

    .klanten-filter,
    .klanten-overzicht {
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 12px 24px rgb(20 25 35 / 0.08);
    }

    .klanten-filter {
        padding: 14px 20px;
    }

    .klanten-filter__form {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        align-items: flex-start;
    }

    .klanten-filter label {
        display: block;
        margin: 0 0 5px;
        color: #3b4149;
        font-size: 12px;
        font-weight: 700;
    }

    .klanten-filter__controls {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .klanten-filter input {
        width: min(280px, 50vw);
        min-height: 31px;
        padding: 0 10px;
        border: 1px solid #d8dde5;
        border-radius: 6px;
        font-size: 13px;
    }

    .klanten-filter input:invalid:not(:placeholder-shown) {
        border-color: #d40a2f;
        box-shadow: 0 0 0 3px rgb(212 10 47 / 0.12);
    }

    .klanten-filter button,
    .klanten-filter a,
    .klanten-detail {
        display: inline-flex;
        min-height: 31px;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
    }

    .klanten-filter button {
        padding: 0 14px;
        border: 0;
        background: #d40a2f;
        color: #fff;
        cursor: pointer;
    }

    .klanten-filter a {
        padding: 0 13px;
        background: #737d8a;
        color: #fff;
    }

    .klanten-field-error,
    .klanten-alert {
        margin: 10px 0 0;
        color: #b00020;
        font-size: 13px;
        font-weight: 700;
    }

    .klanten-alert--success {
        color: #1f6b4b;
    }

    .klanten-overzicht {
        margin-top: 12px;
        padding: 13px 14px 16px;
    }

    .klanten-count {
        margin: 0 0 13px;
        color: #7c8490;
        font-size: 13px;
        font-weight: 700;
    }

    .klanten-table-wrap {
        overflow-x: auto;
    }

    .klanten-table {
        width: 100%;
        min-width: 920px;
        border-collapse: collapse;
        font-size: 13px;
    }

    .klanten-table th {
        background: #d40a2f;
        color: #fff;
        font-weight: 800;
        text-align: left;
    }

    .klanten-table th,
    .klanten-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #edf0f4;
        vertical-align: middle;
    }

    .klanten-table tbody tr:hover {
        background: #fafbfc;
    }

    .klanten-detail {
        min-height: 27px;
        padding: 0 12px;
        border: 1px solid #2f8be6;
        color: #1476d8;
    }

    .klanten-empty {
        color: #3a414a;
        font-weight: 700;
        text-align: center;
    }

    @media (max-width: 760px) {
        .klanten-filter__form {
            display: block;
        }

        .klanten-filter__controls {
            align-items: stretch;
            flex-direction: column;
        }

        .klanten-filter input,
        .klanten-filter button,
        .klanten-filter a {
            width: 100%;
        }

        .klanten-filter input {
            min-height: 38px;
        }
    }
</style>

<main class="page-shell klanten-page">
    <nav class="klanten-breadcrumb" aria-label="Kruimelpad">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <span>Klanten</span>
    </nav>

    <div class="klanten-heading">
        <h1>Overzicht klanten</h1>
    </div>

    <section class="klanten-filter" aria-label="Klanten filteren">
        <form class="klanten-filter__form" method="POST" action="{{ route('klanten.index') }}">
            @csrf

            <label for="postcode">Postcode zoeken</label>
            <div class="klanten-filter__controls">
                <input
                    id="postcode"
                    name="postcode"
                    type="text"
                    value="{{ old('postcode', $postcode ?? '') }}"
                    maxlength="10"
                    pattern="[1-9][0-9]{3}\s?[A-Za-z]{2}"
                    placeholder="Bijv. 3512AB"
                    autocomplete="postal-code"
                    aria-describedby="postcode-fout"
                >
                <button type="submit">Toon Klanten</button>
                <a href="{{ route('klanten.index') }}">Reset</a>
            </div>

            @error('postcode')
                <p id="postcode-fout" class="klanten-field-error">{{ $message }}</p>
            @enderror
        </form>
    </section>

    @if (session('melding'))
        <p class="klanten-alert" role="alert">{{ session('melding') }}</p>
    @endif

    @if ($melding && $klanten->isNotEmpty())
        <p class="klanten-alert {{ $klanten->isNotEmpty() ? 'klanten-alert--success' : '' }}" role="status">
            {{ $melding }}
        </p>
    @endif

    <section class="klanten-overzicht" aria-label="Overzicht klanten met contactgegevens">
        <p class="klanten-count">Gevonden klanten - {{ $klanten->count() }} klant(en)</p>

        <div class="klanten-table-wrap">
            <table class="klanten-table">
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Relatienummer</th>
                        <th>Adres</th>
                        <th>Postcode</th>
                        <th>Woonplaats</th>
                        <th>Mobiel</th>
                        <th>Contact e-mail</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($klanten as $klant)
                        <tr>
                            <td>
                                {{ trim($klant->voornaam . ' ' . ($klant->tussenvoegsel ? $klant->tussenvoegsel . ' ' : '') . $klant->achternaam) }}
                            </td>
                            <td>{{ $klant->relatienummer }}</td>
                            <td>{{ $klant->adres }}</td>
                            <td>{{ $klant->postcode }}</td>
                            <td>{{ $klant->woonplaats }}</td>
                            <td>{{ $klant->mobiel }}</td>
                            <td>{{ $klant->email }}</td>
                            <td>
                                <a class="klanten-detail" href="{{ route('klanten.show', $klant->klant_id) }}">Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="klanten-empty">
                                {{ $melding ?? 'Er zijn geen klanten gevonden.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

@include('components.site-footer')
