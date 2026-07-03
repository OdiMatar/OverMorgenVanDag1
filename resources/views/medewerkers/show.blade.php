@include('components.site-navbar')

{{-- 
    Medewerkers Show View (Detailpagina)
    Toont alle details van een specifieke medewerker en diens contactgegevens.
    Voldoet aan MVC (View), PSR-12, responsiviteit en klantterugkoppeling.
--}}

<main class="page-shell">
    {{-- Succesmelding aan de eindgebruiker (Terugkoppeling acties) --}}
    @if (session('success'))
        <div style="margin-bottom: 24px; padding: 16px; border: 1px solid #d4edda; border-radius: 8px; background-color: #e2f0d9; color: #385723; font-size: 15px; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Broodkruimelpad --}}
    <div style="margin-bottom: 24px; font-size: 14px; color: #8a8f96;">
        <a href="{{ route('home') }}" style="color: #d40a2f; text-decoration: none;">Home</a> /
        <a href="{{ route('medewerkers.index') }}" style="color: #d40a2f; text-decoration: none;">Medewerkers</a> / Detail
    </div>

    {{-- Detailpagina Titel --}}
    <h1 style="color: #d40a2f; font-size: 28px; font-weight: 700; margin-bottom: 24px;">
        Medewerkerdetail <span style="color: #8a8f96; font-weight: 400;">{{ $medewerker->naam }}</span>
    </h1>

    {{-- Detailkaart --}}
    <div style="border: 1px solid #dce1e8; border-radius: 12px; padding: 32px; background: #fff; max-width: 600px; box-shadow: 0 2px 6px rgb(20 25 35 / 0.12); margin-bottom: 32px;">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px; font-size: 14px; text-align: left;">
            <tbody>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: 700; color: #20242a; width: 180px; vertical-align: top;">Naam</td>
                    <td style="padding: 12px 0; color: #69717c; vertical-align: top;">{{ $medewerker->naam }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: 700; color: #20242a; vertical-align: top;">Specialisatie</td>
                    <td style="padding: 12px 0; color: #69717c; vertical-align: top;">{{ $medewerker->Specialisatie }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: 700; color: #20242a; vertical-align: top;">Geboortedatum</td>
                    <td style="padding: 12px 0; color: #69717c; vertical-align: top;">{{ $medewerker->Geboortedatum ? $medewerker->Geboortedatum->format('d-m-Y') : '' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: 700; color: #20242a; vertical-align: top;">Contact e-mail</td>
                    <td style="padding: 12px 0; color: #69717c; vertical-align: top;">{{ $medewerker->contact->Email ?? '' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: 700; color: #20242a; vertical-align: top;">Account e-mail</td>
                    <td style="padding: 12px 0; color: #69717c; vertical-align: top;">{{ $medewerker->user->email ?? '' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: 700; color: #20242a; vertical-align: top;">Straatnaam</td>
                    <td style="padding: 12px 0; color: #69717c; vertical-align: top;">{{ $medewerker->contact->Straatnaam ?? '' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: 700; color: #20242a; vertical-align: top;">Huisnummer</td>
                    <td style="padding: 12px 0; color: #69717c; vertical-align: top;">{{ $medewerker->contact->Huisnummer ?? '' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: 700; color: #20242a; vertical-align: top;">Toevoeging</td>
                    <td style="padding: 12px 0; color: #69717c; vertical-align: top;">{{ $medewerker->contact->Toevoeging ?: '-' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: 700; color: #20242a; vertical-align: top;">Postcode</td>
                    <td style="padding: 12px 0; color: #69717c; vertical-align: top;">{{ $medewerker->contact->Postcode ?? '' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: 700; color: #20242a; vertical-align: top;">Plaats</td>
                    <td style="padding: 12px 0; color: #69717c; vertical-align: top;">{{ $medewerker->contact->Plaats ?? '' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: 700; color: #20242a; vertical-align: top;">Mobiel</td>
                    <td style="padding: 12px 0; color: #69717c; vertical-align: top;">{{ $medewerker->contact->Mobiel ?? '' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px 0; font-weight: 700; color: #20242a; vertical-align: top;">Opmerking</td>
                    <td style="padding: 12px 0; color: #69717c; vertical-align: top;">{{ $medewerker->Opmerking ?: '-' }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Actieknoppen --}}
        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('medewerkers.edit', $medewerker->Id) }}" style="display: inline-flex; min-height: 38px; align-items: center; justify-content: center; padding: 0 16px; border-radius: 7px; background: #d40a2f; color: #fff; font-weight: 700; text-decoration: none; font-size: 14px;">Wijzigen</a>
            <a href="{{ route('medewerkers.index') }}" style="display: inline-flex; min-height: 38px; align-items: center; justify-content: center; padding: 0 16px; border: 1px solid #1c78b2; border-radius: 7px; color: #1c78b2; font-weight: 700; text-decoration: none; font-size: 14px; background: #fff;">Terug</a>
        </div>
    </div>
</main>

@include('components.site-footer')
