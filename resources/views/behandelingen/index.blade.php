@include('components.site-navbar')

<main class="page-shell behandelingen-page">
    {{-- Overzicht met filter, telling en paginering van behandelingen. --}}
    @if (($scherm ?? 'overzicht') === 'overzicht')
        <nav class="behandelingen-breadcrumb" aria-label="Kruimelpad">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <span>Behandelingen</span>
        </nav>

        <h1 class="behandelingen-title">Overzicht behandelingen</h1>

        <section class="behandelingen-filter-card" aria-label="Behandeling filteren">
            <form class="behandelingen-filter-form" method="GET" action="{{ route('behandelingen.index') }}">
                <label for="behandeling">Behandeling selecteren</label>
                <div class="behandelingen-filter-row">
                    <select id="behandeling" name="behandeling">
                        @foreach ($behandelingOpties as $optie)
                            <option value="{{ $optie }}" @selected($selectedBehandeling === $optie)>{{ $optie }}</option>
                        @endforeach
                    </select>
                    <button class="behandelingen-primary-button" type="submit">Maak selectie</button>
                    <a class="behandelingen-reset-button" href="{{ route('behandelingen.index') }}">Reset</a>
                </div>
            </form>
        </section>

        <section class="behandelingen-table-card">
            <p class="behandelingen-count">
                Gevonden behandelingen - {{ $behandelingen->total() }} behandeling(en)
            </p>

            @if ($behandelingen->lastPage() > 1)
                <nav class="behandelingen-pagination" aria-label="Paginering behandelingen">
                    @if ($behandelingen->onFirstPage())
                        <span class="behandelingen-page-link is-disabled">&lsaquo;</span>
                    @else
                        <a class="behandelingen-page-link" href="{{ $behandelingen->previousPageUrl() }}" aria-label="Vorige pagina">&lsaquo;</a>
                    @endif

                    @for ($pagina = 1; $pagina <= $behandelingen->lastPage(); $pagina++)
                        @if ($pagina === $behandelingen->currentPage())
                            <span class="behandelingen-page-link is-active">{{ $pagina }}</span>
                        @else
                            <a class="behandelingen-page-link" href="{{ $behandelingen->url($pagina) }}">{{ $pagina }}</a>
                        @endif
                    @endfor

                    @if ($behandelingen->hasMorePages())
                        <a class="behandelingen-page-link" href="{{ $behandelingen->nextPageUrl() }}" aria-label="Volgende pagina">&rsaquo;</a>
                    @else
                        <span class="behandelingen-page-link is-disabled">&rsaquo;</span>
                    @endif
                </nav>
            @endif

            <div class="behandelingen-table-wrap">
                <table class="behandelingen-table">
                    <thead>
                        <tr>
                            <th>Soort</th>
                            <th>Omschrijving</th>
                            <th>Duur</th>
                            <th>Prijs</th>
                            <th>Aantal producten</th>
                            <th>Actie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($behandelingen as $behandeling)
                            <tr>
                                <td>{{ $behandeling->Naam }}</td>
                                <td>{{ $behandeling->Omschrijving }}</td>
                                <td>{{ $behandeling->Duurminuten }} min</td>
                                <td>EUR {{ number_format((float) $behandeling->Prijs, 2, ',', '.') }}</td>
                                <td>{{ $behandeling->AantalProducten }}</td>
                                <td>
                                    <a class="behandelingen-product-button" href="{{ route('behandelingen.producten.index', $behandeling->Id) }}">Producten</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="behandelingen-empty-message" colspan="6">
                                    Er zijn geen behandelingen bekend met deze naam
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    {{-- Productlijst die bij de geselecteerde behandeling hoort. --}}
    @elseif ($scherm === 'producten')
        <nav class="behandelingen-breadcrumb" aria-label="Kruimelpad">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <a href="{{ route('behandelingen.index') }}">Behandelingen</a>
            <span>/</span>
            <span>Detail</span>
        </nav>

        <h1 class="behandelingen-title">Producten per behandeling <span>{{ $behandeling->Naam }}</span></h1>

        <section class="product-panel">
            <div class="behandelingen-table-wrap">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Merk</th>
                            <th>Omschrijving</th>
                            <th>EAN-code</th>
                            <th>Aantal op voorraad</th>
                            <th>Verkoopprijs</th>
                            <th>Actie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($producten as $product)
                            <tr>
                                <td>{{ $product->Naam }}</td>
                                <td>{{ $product->Merk }}</td>
                                <td>{{ $product->Omschrijving }}</td>
                                <td>{{ $product->EANcode }}</td>
                                <td>{{ $product->AantalOpVoorraad }}</td>
                                <td>EUR {{ number_format((float) $product->VerkoopPrijs, 2, ',', '.') }}</td>
                                <td>
                                    <a class="product-primary-button" href="{{ route('behandelingen.producten.show', [$behandeling->Id, $product->Id]) }}">Details</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="behandelingen-empty-message" colspan="7">
                                    Er zijn geen producten bekend bij deze behandeling
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="product-actions">
                <a class="product-secondary-button" href="{{ route('behandelingen.index') }}">Terug</a>
            </div>
        </section>
    {{-- Detailpagina van een product binnen een behandeling. --}}
    @elseif ($scherm === 'detail')
        @if (session('status_success'))
            <div class="product-alert product-alert-success" data-auto-hide>
                {{ session('status_success') }}
            </div>
        @endif

        <nav class="behandelingen-breadcrumb" aria-label="Kruimelpad">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <a href="{{ route('behandelingen.index') }}">Behandelingen</a>
            <span>/</span>
            <span>Detail</span>
        </nav>

        <h1 class="behandelingen-title">Productdetail <span>{{ $product->Naam }}</span></h1>

        <section class="product-detail-card">
            <dl class="product-detail-list">
                @foreach ([
                    'Product' => $product->Naam,
                    'Merk' => $product->Merk,
                    'Omschrijving' => $product->Omschrijving,
                    'EAN-code' => $product->EANcode,
                    'Houdbaarheidsdatum' => \Carbon\Carbon::parse($product->Houdbaarheidsdatum)->format('d-m-Y'),
                    'Inkoopprijs' => 'EUR ' . number_format((float) $product->InkoopPrijs, 2, ',', '.'),
                    'Verkoopprijs' => 'EUR ' . number_format((float) $product->VerkoopPrijs, 2, ',', '.'),
                    'Aantal op voorraad' => $product->AantalOpVoorraad,
                    'Leverancier' => $product->LeverancierNaam ?? 'Onbekend',
                    'Postcode leverancier' => $product->LeverancierPostcode ?? '-',
                    'Plaats leverancier' => $product->LeverancierPlaats ?? '-',
                    'E-mail leverancier' => $product->LeverancierEmail ?? '-',
                    'Mobiel leverancier' => $product->LeverancierMobiel ?? '-',
                    'Opmerking' => $product->Opmerking ?: 'Geschikt voor dagelijks salongebruik.',
                ] as $label => $waarde)
                    <div>
                        <dt>{{ $label }}</dt>
                        <dd>{{ $waarde }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="product-actions">
                <a class="product-primary-button" href="{{ route('behandelingen.producten.edit', [$behandeling->Id, $product->Id]) }}">Wijzigen</a>
                <a class="product-secondary-button" href="{{ route('behandelingen.producten.index', $behandeling->Id) }}">Terug</a>
            </div>
        </section>
    {{-- Wijzigformulier voor de verkoopprijs van een gekoppeld product. --}}
    @elseif ($scherm === 'wijzigen')
        @if (session('status_error'))
            <div class="product-alert product-alert-error">
                {{ session('status_error') }}
            </div>
        @endif

        <nav class="behandelingen-breadcrumb" aria-label="Kruimelpad">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <a href="{{ route('behandelingen.index') }}">Behandelingen</a>
            <span>/</span>
            <span>Wijzigen</span>
        </nav>

        <h1 class="behandelingen-title">Product wijzigen <span>{{ $product->Naam }}</span></h1>

        <section class="product-form-card">
            <form method="POST" action="{{ route('behandelingen.producten.update', [$behandeling->Id, $product->Id]) }}">
                @csrf
                @method('PUT')

                <div class="product-form-grid">
                    <label>Product <input type="text" value="{{ $product->Naam }}" disabled></label>
                    <label>Merk <input type="text" value="{{ $product->Merk }}" disabled></label>
                    <label>Omschrijving <input type="text" value="{{ $product->Omschrijving }}" disabled></label>
                    <label>EAN-code <input type="text" value="{{ $product->EANcode }}" disabled></label>
                    <label>Houdbaarheidsdatum <input type="text" value="{{ \Carbon\Carbon::parse($product->Houdbaarheidsdatum)->format('d-m-Y') }}" disabled></label>
                    <label>Aantal op voorraad <input type="text" value="{{ $product->AantalOpVoorraad }}" disabled></label>
                    <label>Inkoopprijs <input type="text" value="EUR {{ number_format((float) $product->InkoopPrijs, 2, ',', '.') }}" disabled></label>
                    <label>Leverancier <input type="text" value="{{ $product->LeverancierNaam ?? 'Onbekend' }}" disabled></label>
                    <label>Huidige verkoopprijs <input type="text" value="EUR {{ number_format((float) $product->VerkoopPrijs, 2, ',', '.') }}" disabled></label>
                    <label>Plaats leverancier <input type="text" value="{{ $product->LeverancierPlaats ?? '-' }}" disabled></label>
                    <label>
                        <span class="product-field-label">Nieuwe verkoopprijs <span>*</span></span>
                        <div class="product-input-with-status">
                            <input
                                @class(['is-invalid' => isset($errors) && $errors->has('nieuwe_verkoopprijs')])
                                type="text"
                                name="nieuwe_verkoopprijs"
                                value="{{ old('nieuwe_verkoopprijs', number_format((float) $product->VerkoopPrijs, 2, ',', '')) }}"
                            >
                            @if (isset($errors) && $errors->has('nieuwe_verkoopprijs'))
                                <span class="product-input-status" aria-hidden="true">!</span>
                            @endif
                        </div>
                        @if (isset($errors) && $errors->has('nieuwe_verkoopprijs'))
                            <strong class="product-field-error">{{ $errors->first('nieuwe_verkoopprijs') }}</strong>
                        @endif
                        <small>Minimaal 30 procent boven de inkoopprijs.</small>
                    </label>
                    <label>Opmerking <input type="text" value="{{ $product->Opmerking ?: 'Geschikt voor dagelijks salongebruik.' }}" disabled></label>
                </div>

                <p class="product-required-note">Velden met een <span>*</span> zijn verplicht.</p>

                <div class="product-actions">
                    <button class="product-primary-button" type="submit">Opslaan</button>
                    <a class="product-reset-button" href="{{ route('behandelingen.producten.show', [$behandeling->Id, $product->Id]) }}">Terug</a>
                </div>
            </form>
        </section>
    @endif
</main>

@if (session('status_success'))
    <script>
        window.setTimeout(function () {
            document.querySelectorAll('[data-auto-hide]').forEach(function (element) {
                element.style.display = 'none';
            });
        }, 3000);
    </script>
@endif

@include('components.site-footer')
