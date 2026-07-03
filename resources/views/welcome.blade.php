@include('components.site-navbar')

<main class="page-shell">
    <section style="
        min-height: 548px;
        padding: 44px;
        border: 1px solid #dce1e8;
        border-radius: 12px;
        background: linear-gradient(105deg, #fff 0%, #fff 70%, #fff6e8 100%);
        box-shadow: 0 2px 6px rgb(20 25 35 / 0.12);
    ">
        <span style="
            display: inline-flex;
            min-height: 18px;
            align-items: center;
            padding: 2px 7px;
            border-radius: 4px;
            background: #ffc400;
            color: #111;
            font-size: 11px;
            font-weight: 800;
        ">Kapsalon applicatie</span>

        @guest
            <h1 style="margin: 18px 0 14px; color: #6d747d; font-size: 32px; line-height: 1.1;">Welkom bij Kniploket Tiko</h1>
            <p style="max-width: 680px; margin: 0 0 24px; color: #8a8f96; font-size: 17px; line-height: 1.5;">
                Log in om verder te gaan naar je omgeving of maak een account aan als je nog geen account hebt.
            </p>

            <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                <a href="{{ route('login') }}" style="
                    display: inline-flex;
                    min-height: 38px;
                    align-items: center;
                    justify-content: center;
                    padding: 0 16px;
                    border-radius: 7px;
                    background: #d40a2f;
                    color: #fff;
                    font-size: 14px;
                    font-weight: 700;
                    text-decoration: none;
                ">Inloggen</a>

                <a href="{{ route('register') }}" style="
                    display: inline-flex;
                    min-height: 38px;
                    align-items: center;
                    justify-content: center;
                    padding: 0 16px;
                    border: 1px solid #3383b9;
                    border-radius: 7px;
                    color: #1c78b2;
                    font-size: 14px;
                    font-weight: 700;
                    text-decoration: none;
                ">Account aanmaken</a>
            </div>
        @else
            <h1 style="margin: 18px 0 14px; color: #6d747d; font-size: 28px; line-height: 1.1;">
                @if (auth()->user()->isEigenaar())
                    Eigenaar
                @else
                    Home
                @endif
            </h1>

            <p style="margin: 0 0 19px; color: #69717c; font-size: 14px; font-weight: 700;">Home</p>

            @if (auth()->user()->isEigenaar())
                <p style="margin: 0 0 24px; color: #8a8f96; font-size: 17px; line-height: 1.5;">
                    Welkom bij Kniploket Tiko - hier regel je eenvoudig klanten, medewerkers, behandelingen en producten voor de salon.
                </p>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">
                    @foreach ([
                        ['titel' => 'Klanten', 'tekst' => 'Bekijk klantgegevens en contactinformatie.', 'route' => 'klanten.index'],
                        ['titel' => 'Medewerkers', 'tekst' => 'Overzicht van medewerkers en hun basisgegevens.', 'route' => 'medewerkers.index'],
                        ['titel' => 'Behandelingen', 'tekst' => 'Overzicht van behandelingen, duur en prijsinformatie.', 'route' => 'behandelingen.index'],
                        ['titel' => 'Producten', 'tekst' => 'Bekijk producten binnen het assortiment.', 'route' => 'producten.index'],
                    ] as $kaart)
                        <article style="
                            min-height: 142px;
                            padding: 17px 14px;
                            border-radius: 8px;
                            background: #fff;
                            box-shadow: 0 16px 28px rgb(25 30 40 / 0.08);
                        ">
                            <h2 style="margin: 0 0 8px; font-size: 19px; line-height: 1.2;">{{ $kaart['titel'] }}</h2>
                            <p style="min-height: 42px; margin: 0 0 16px; color: #737982; font-size: 15px; line-height: 1.45;">{{ $kaart['tekst'] }}</p>
                            <a href="{{ route($kaart['route']) }}" style="
                                display: inline-flex;
                                min-height: 29px;
                                align-items: center;
                                justify-content: center;
                                padding: 0 10px;
                                border: 1px solid #3383b9;
                                border-radius: 6px;
                                color: #1c78b2;
                                font-size: 13px;
                                font-weight: 700;
                                text-decoration: none;
                            ">Openen</a>
                        </article>
                    @endforeach
                </div>
            @else
                <p style="max-width: 680px; margin: 0; color: #8a8f96; font-size: 17px; line-height: 1.5;">
                    Je bent ingelogd. Alleen de eigenaar kan de onderdelen Klanten, Medewerkers, Behandelingen en Producten openen.
                </p>
            @endif
        @endguest
    </section>
</main>

@include('components.site-footer')
