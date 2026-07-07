@include('components.site-navbar')

{{--
    Medewerkers Index View (Overzichtpagina)
    Toont een tabel van alle medewerkers en contactgegevens, met paginering en filters.
    Voldoet aan MVC (View), PSR-12, responsiviteit en klantterugkoppeling.
--}}

<style>
    .medewerkers-shell {
        max-width: 940px;
        min-height: calc(100vh - 118px);
        margin: 36px auto 0;
        padding: 0 24px;
    }

    .medewerkers-breadcrumb {
        margin-bottom: 16px;
        color: #8a93a1;
        font-size: 13px;
        font-weight: 700;
    }

    .medewerkers-breadcrumb a {
        color: #d40a2f;
        text-decoration: none;
    }

    .medewerkers-title {
        margin: 0 0 8px;
        color: #d40a2f;
        font-size: 19px;
        font-weight: 800;
        line-height: 1.2;
    }

    .medewerkers-filter-card,
    .medewerkers-table-card {
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 18px 35px rgb(35 45 65 / 0.08);
    }

    .medewerkers-filter-card {
        min-height: 64px;
        margin-bottom: 8px;
        padding: 10px 12px;
    }

    .medewerkers-filter-form {
        width: min(100%, 405px);
        margin-left: auto;
    }

    .medewerkers-filter-form label {
        display: block;
        margin-bottom: 5px;
        color: #202b3f;
        font-size: 11px;
        font-weight: 800;
    }

    .medewerkers-filter-row {
        display: grid;
        grid-template-columns: minmax(160px, 1fr) 110px 60px;
        gap: 7px;
        align-items: center;
    }

    .medewerkers-filter-row select {
        width: 100%;
        min-height: 30px;
        padding: 0 9px;
        border: 1px solid #d6dde6;
        border-radius: 7px;
        background: #fff;
        color: #303845;
        font: inherit;
        font-size: 12px;
        cursor: pointer;
    }

    .medewerkers-primary-button,
    .medewerkers-reset-button {
        display: inline-flex;
        min-height: 30px;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
    }

    .medewerkers-primary-button {
        border: 0;
        background: #d40a2f;
        color: #fff;
        cursor: pointer;
    }

    .medewerkers-reset-button {
        background: #737f8d;
        color: #fff;
    }

    .medewerkers-table-card {
        overflow: hidden;
        padding: 11px 8px 7px;
    }

    .medewerkers-count {
        margin: 0 0 8px 4px;
        color: #8692a5;
        font-size: 12px;
    }

    .medewerkers-pagination {
        display: flex;
        justify-content: center;
        gap: 6px;
        margin-bottom: 9px;
    }

    .medewerkers-page-link {
        display: inline-grid;
        min-width: 28px;
        height: 28px;
        place-items: center;
        border: 1px solid #dde5ef;
        border-radius: 7px;
        color: #d40a2f;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }

    .medewerkers-page-link.is-active {
        border-color: #d40a2f;
        background: #d40a2f;
        color: #fff;
    }

    .medewerkers-page-link.is-disabled {
        color: #cbd3dd;
        cursor: not-allowed;
    }

    .medewerkers-table-wrap {
        overflow-x: auto;
    }

    .medewerkers-table {
        width: 100%;
        border-collapse: collapse;
        color: #202b3f;
        font-size: 12px;
        text-align: left;
    }

    .medewerkers-table thead {
        background: #d40a2f;
        color: #fff;
    }

    .medewerkers-table th,
    .medewerkers-table td {
        padding: 9px 10px;
        border-bottom: 1px solid #e5e9ef;
        vertical-align: middle;
    }

    .medewerkers-table th {
        font-weight: 800;
    }

    .medewerkers-table th:first-child {
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
    }

    .medewerkers-table th:last-child {
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
        text-align: center;
    }

    .medewerkers-table td:first-child {
        font-weight: 800;
    }

    .medewerkers-table th:nth-child(4),
    .medewerkers-table td:nth-child(4),
    .medewerkers-table th:nth-child(5),
    .medewerkers-table td:nth-child(5),
    .medewerkers-table th:nth-child(6),
    .medewerkers-table td:nth-child(6),
    .medewerkers-table th:nth-child(7),
    .medewerkers-table td:nth-child(7) {
        white-space: nowrap;
    }

    .medewerkers-details-button {
        display: inline-flex;
        min-width: 58px;
        min-height: 26px;
        align-items: center;
        justify-content: center;
        border: 1px solid #1e78ff;
        border-radius: 7px;
        background: #fff;
        color: #1e78ff;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
    }

    .medewerkers-empty-message {
        height: 44px;
        color: #737f8d;
        text-align: center;
    }

    @media (max-width: 760px) {
        .medewerkers-shell {
            margin-top: 24px;
            padding: 0 14px;
        }

        .medewerkers-filter-form {
            width: 100%;
        }

        .medewerkers-filter-row {
            grid-template-columns: 1fr;
        }

        .medewerkers-primary-button,
        .medewerkers-reset-button {
            width: 100%;
        }
    }
</style>

<main class="medewerkers-shell">
    {{-- Breadcrumbs (Broodkruimelpad) --}}
    <div class="medewerkers-breadcrumb">
        <a href="{{ route('home') }}">Home</a> / Medewerkers
    </div>

    {{-- Hoofdtitel --}}
    <h1 class="medewerkers-title">Overzicht medewerkers</h1>

    {{-- Filterkaart voor specialisaties --}}
    <section class="medewerkers-filter-card" aria-label="Medewerkers filteren">
        <form class="medewerkers-filter-form" method="GET" action="{{ route('medewerkers.index') }}">
            <label for="specialisatie">Specialisatie</label>
            <div class="medewerkers-filter-row">
                <select id="specialisatie" name="specialisatie">
                    <option value="all" {{ $specialisatie == 'all' || !$specialisatie ? 'selected' : '' }}>Alle specialisaties</option>
                    @foreach($specialisaties as $spec)
                        <option value="{{ $spec }}" {{ $specialisatie == $spec ? 'selected' : '' }}>{{ $spec }}</option>
                    @endforeach
                </select>
                <button class="medewerkers-primary-button" type="submit">Toon medewerkers</button>
                <a class="medewerkers-reset-button" href="{{ route('medewerkers.index') }}">Reset</a>
            </div>
        </form>
    </section>

    {{-- Resultaten Kaart --}}
    <section class="medewerkers-table-card" aria-label="Medewerkers resultaten">
        {{-- Resultatenteller & Paginering (Eindgebruiker feedback) --}}
        <p class="medewerkers-count">
            Gevonden medewerkers - {{ $totalFound }} medewerker(s)
        </p>

        {{-- Paginering (Indien meer dan 1 pagina) --}}
        @if ($medewerkers->lastPage() > 1)
            <nav class="medewerkers-pagination" aria-label="Paginering medewerkers">
                {{-- Vorige pagina link --}}
                @if ($medewerkers->onFirstPage())
                    <span class="medewerkers-page-link is-disabled" aria-disabled="true">&lsaquo;</span>
                @else
                    <a class="medewerkers-page-link" href="{{ $medewerkers->appends(request()->query())->previousPageUrl() }}" aria-label="Vorige pagina">&lsaquo;</a>
                @endif

                {{-- Paginanummers --}}
                @for ($i = 1; $i <= $medewerkers->lastPage(); $i++)
                    @if ($i == $medewerkers->currentPage())
                        <span class="medewerkers-page-link is-active" aria-current="page">{{ $i }}</span>
                    @else
                        <a class="medewerkers-page-link" href="{{ $medewerkers->appends(request()->query())->url($i) }}">{{ $i }}</a>
                    @endif
                @endfor

                {{-- Volgende pagina link --}}
                @if ($medewerkers->hasMorePages())
                    <a class="medewerkers-page-link" href="{{ $medewerkers->appends(request()->query())->nextPageUrl() }}" aria-label="Volgende pagina">&rsaquo;</a>
                @else
                    <span class="medewerkers-page-link is-disabled" aria-disabled="true">&rsaquo;</span>
                @endif
            </nav>
        @endif

        {{-- Medewerkers Tabel --}}
        <div class="medewerkers-table-wrap">
            <table class="medewerkers-table">
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Specialisatie</th>
                        <th>Adres</th>
                        <th>Postcode</th>
                        <th>Woonplaats</th>
                        <th>Mobiel</th>
                        <th>Contact e-mail</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medewerkers as $medewerker)
                        <tr>
                            <td>{{ $medewerker->naam }}</td>
                            <td>{{ $medewerker->Specialisatie }}</td>
                            {{-- Indien contactgegevens door JOIN / Eloquent zijn geladen, toon deze --}}
                            <td>
                                {{ $medewerker->Straatnaam ?? ($medewerker->contact->Straatnaam ?? '') }}
                                {{ $medewerker->Huisnummer ?? ($medewerker->contact->Huisnummer ?? '') }}
                                {{ $medewerker->Toevoeging ?? ($medewerker->contact->Toevoeging ?? '') }}
                            </td>
                            <td>{{ $medewerker->Postcode ?? ($medewerker->contact->Postcode ?? '') }}</td>
                            <td>{{ $medewerker->Plaats ?? ($medewerker->contact->Plaats ?? '') }}</td>
                            <td>{{ $medewerker->Mobiel ?? ($medewerker->contact->Mobiel ?? '') }}</td>
                            <td>{{ $medewerker->ContactEmail ?? ($medewerker->contact->Email ?? '') }}</td>
                            <td style="text-align: center;">
                                <a class="medewerkers-details-button" href="{{ route('medewerkers.show', $medewerker->Id) }}">Details</a>
                            </td>
                        </tr>
                    @empty
                        {{-- Lege status melding conform de user story en wireframe --}}
                        <tr>
                            <td class="medewerkers-empty-message" colspan="8">
                                Er zijn geen medewerkers bekend met de geselecteerde specialisatie
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

@include('components.site-footer')
