@include('components.site-navbar')

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
        <form class="klanten-filter__form" method="GET" action="{{ route('klanten.index') }}">
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

    @if ($melding)
        <p class="klanten-alert" role="status">{{ $melding }}</p>
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
                            <td><span class="klanten-detail">Details</span></td>
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
