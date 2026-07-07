@include('components.site-navbar')

{{-- 
    Medewerkers Index View (Overzichtpagina)
    Toont een tabel van alle medewerkers en contactgegevens, met paginering en filters.
    Voldoet aan MVC (View), PSR-12, responsiviteit en klantterugkoppeling.
--}}

<main class="page-shell">
    {{-- Breadcrumbs (Broodkruimelpad) --}}
    <div style="margin-bottom: 24px; font-size: 14px; color: #8a8f96;">
        <a href="{{ route('home') }}" style="color: #d40a2f; text-decoration: none;">Home</a> / Medewerkers
    </div>

    {{-- Hoofdtitel --}}
    <h1 style="color: #d40a2f; font-size: 28px; font-weight: 700; margin-bottom: 24px;">Overzicht medewerkers</h1>

    {{-- Filterkaart voor specialisaties --}}
    <div style="border: 1px solid #dce1e8; border-radius: 12px; padding: 24px; background: #fff; margin-bottom: 24px; display: flex; align-items: flex-end; justify-content: flex-end; box-shadow: 0 2px 6px rgb(20 25 35 / 0.08);">
        <form method="GET" action="{{ route('medewerkers.index') }}" style="display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap;">
            <label style="display: grid; gap: 6px; color: #20242a; font-weight: 700; font-size: 14px;">
                Specialisatie
                <select name="specialisatie" style="min-height: 38px; padding: 8px 12px; border: 1px solid #cfd6df; border-radius: 7px; font: inherit; min-width: 240px; background: #fff; cursor: pointer;">
                    <option value="all" {{ $specialisatie == 'all' || !$specialisatie ? 'selected' : '' }}>Alle specialisaties</option>
                    @foreach($specialisaties as $spec)
                        <option value="{{ $spec }}" {{ $specialisatie == $spec ? 'selected' : '' }}>{{ $spec }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" style="min-height: 38px; padding: 0 16px; border: 0; border-radius: 7px; background: #d40a2f; color: #fff; font: inherit; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;">Toon medewerkers</button>
            <a href="{{ route('medewerkers.index') }}" style="min-height: 38px; padding: 0 16px; border: 0; border-radius: 7px; background: #737982; color: #fff; font: inherit; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">Reset</a>
        </form>
    </div>

    {{-- Resultaten Kaart --}}
    <div style="border: 1px solid #dce1e8; border-radius: 12px; background: #fff; padding: 24px; box-shadow: 0 2px 6px rgb(20 25 35 / 0.08);">
        
        {{-- Resultatenteller & Paginering (Eindgebruiker feedback) --}}
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 16px; gap: 12px;">
            <div style="color: #8a8f96; font-size: 14px;">
                Gevonden medewerkers - {{ $totalFound }} medewerker(s)
            </div>
            
            {{-- Paginering (Indien meer dan 1 pagina) --}}
            @if ($medewerkers->lastPage() > 1)
                <div style="display: flex; gap: 6px; margin: 0 auto;">
                    {{-- Vorige pagina link --}}
                    @if ($medewerkers->onFirstPage())
                        <span style="display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center; border: 1px solid #dce1e8; border-radius: 6px; color: #8a8f96; cursor: not-allowed; font-size: 14px;">&lsaquo;</span>
                    @else
                        <a href="{{ $medewerkers->appends(request()->query())->previousPageUrl() }}" style="display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center; border: 1px solid #dce1e8; border-radius: 6px; color: #d40a2f; text-decoration: none; font-size: 14px;">&lsaquo;</a>
                    @endif

                    {{-- Paginanummers --}}
                    @for ($i = 1; $i <= $medewerkers->lastPage(); $i++)
                        @if ($i == $medewerkers->currentPage())
                            <span style="display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center; border-radius: 6px; background: #d40a2f; color: #fff; font-weight: 700; font-size: 14px;">{{ $i }}</span>
                        @else
                            <a href="{{ $medewerkers->appends(request()->query())->url($i) }}" style="display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center; border: 1px solid #dce1e8; border-radius: 6px; color: #d40a2f; text-decoration: none; font-size: 14px;">{{ $i }}</a>
                        @endif
                    @endfor

                    {{-- Volgende pagina link --}}
                    @if ($medewerkers->hasMorePages())
                        <a href="{{ $medewerkers->appends(request()->query())->nextPageUrl() }}" style="display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center; border: 1px solid #dce1e8; border-radius: 6px; color: #d40a2f; text-decoration: none; font-size: 14px;">&rsaquo;</a>
                    @else
                        <span style="display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center; border: 1px solid #dce1e8; border-radius: 6px; color: #8a8f96; cursor: not-allowed; font-size: 14px;">&rsaquo;</span>
                    @endif
                </div>
            @endif
            <div style="width: 150px; display: inline-block;"></div> {{-- Spacer voor uitlijning --}}
        </div>

        {{-- Medewerkers Tabel --}}
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                <thead>
                    <tr style="background: #d40a2f; color: #fff;">
                        <th style="padding: 12px 16px; font-weight: 700; border-top-left-radius: 6px; border-bottom-left-radius: 6px;">Naam</th>
                        <th style="padding: 12px 16px; font-weight: 700;">Specialisatie</th>
                        <th style="padding: 12px 16px; font-weight: 700;">Adres</th>
                        <th style="padding: 12px 16px; font-weight: 700;">Postcode</th>
                        <th style="padding: 12px 16px; font-weight: 700;">Woonplaats</th>
                        <th style="padding: 12px 16px; font-weight: 700;">Mobiel</th>
                        <th style="padding: 12px 16px; font-weight: 700;">Contact e-mail</th>
                        <th style="padding: 12px 16px; font-weight: 700; border-top-right-radius: 6px; border-bottom-right-radius: 6px; text-align: center;">Actie</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medewerkers as $medewerker)
                        <tr style="border-bottom: 1px solid #eef2f6;">
                            <td style="padding: 16px; font-weight: 700; color: #20242a;">{{ $medewerker->naam }}</td>
                            <td style="padding: 16px; color: #20242a;">{{ $medewerker->Specialisatie }}</td>
                            {{-- Indien contactgegevens door JOIN / Eloquent zijn geladen, toon deze --}}
                            <td style="padding: 16px; color: #20242a;">
                                {{ $medewerker->Straatnaam ?? ($medewerker->contact->Straatnaam ?? '') }} 
                                {{ $medewerker->Huisnummer ?? ($medewerker->contact->Huisnummer ?? '') }}
                                {{ $medewerker->Toevoeging ?? ($medewerker->contact->Toevoeging ?? '') }}
                            </td>
                            <td style="padding: 16px; color: #20242a;">{{ $medewerker->Postcode ?? ($medewerker->contact->Postcode ?? '') }}</td>
                            <td style="padding: 16px; color: #20242a;">{{ $medewerker->Plaats ?? ($medewerker->contact->Plaats ?? '') }}</td>
                            <td style="padding: 16px; color: #20242a;">{{ $medewerker->Mobiel ?? ($medewerker->contact->Mobiel ?? '') }}</td>
                            <td style="padding: 16px; color: #20242a;">{{ $medewerker->ContactEmail ?? ($medewerker->contact->Email ?? '') }}</td>
                            <td style="padding: 16px; text-align: center;">
                                <a href="{{ route('medewerkers.show', $medewerker->Id) }}" style="display: inline-flex; min-height: 28px; align-items: center; justify-content: center; padding: 0 16px; border: 1px solid #1c78b2; border-radius: 6px; color: #1c78b2; font-size: 13px; font-weight: 700; text-decoration: none; background: #fff;">Details</a>
                            </td>
                        </tr>
                    @empty
                        {{-- Lege status melding conform de user story en wireframe --}}
                        <tr>
                            <td colspan="8" style="padding: 24px; text-align: center; color: #737982; font-size: 15px;">
                                Er zijn geen medewerkers bekend met de geselecteerde specialisatie
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>

@include('components.site-footer')
