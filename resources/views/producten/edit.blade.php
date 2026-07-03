@include('components.site-navbar')

<main class="page-shell behandelingen-page">
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
                <label>
                    Product
                    <input type="text" value="{{ $product->Naam }}" disabled>
                </label>
                <label>
                    Merk
                    <input type="text" value="{{ $product->Merk }}" disabled>
                </label>
                <label>
                    Omschrijving
                    <input type="text" value="{{ $product->Omschrijving }}" disabled>
                </label>
                <label>
                    EAN-code
                    <input type="text" value="{{ $product->EANcode }}" disabled>
                </label>
                <label>
                    Houdbaarheidsdatum
                    <input type="text" value="{{ \Carbon\Carbon::parse($product->Houdbaarheidsdatum)->format('d-m-Y') }}" disabled>
                </label>
                <label>
                    Aantal op voorraad
                    <input type="text" value="{{ $product->AantalOpVoorraad }}" disabled>
                </label>
                <label>
                    Inkoopprijs
                    <input type="text" value="EUR {{ number_format((float) $product->InkoopPrijs, 2, ',', '.') }}" disabled>
                </label>
                <label>
                    Leverancier
                    <input type="text" value="{{ $product->LeverancierNaam ?? 'Onbekend' }}" disabled>
                </label>
                <label>
                    Huidige verkoopprijs
                    <input type="text" value="EUR {{ number_format((float) $product->VerkoopPrijs, 2, ',', '.') }}" disabled>
                </label>
                <label>
                    Plaats leverancier
                    <input type="text" value="{{ $product->LeverancierPlaats ?? '-' }}" disabled>
                </label>
                <label>
                    Nieuwe verkoopprijs <span>*</span>
                    <input
                        @class(['is-invalid' => isset($errors) && $errors->has('nieuwe_verkoopprijs')])
                        type="text"
                        name="nieuwe_verkoopprijs"
                        value="{{ old('nieuwe_verkoopprijs', number_format((float) $product->VerkoopPrijs, 2, ',', '')) }}"
                    >
                    @if (isset($errors) && $errors->has('nieuwe_verkoopprijs'))
                        <strong class="product-field-error">{{ $errors->first('nieuwe_verkoopprijs') }}</strong>
                    @endif
                    <small>Minimaal 30 procent boven de inkoopprijs.</small>
                </label>
                <label>
                    Opmerking
                    <input type="text" value="{{ $product->Opmerking ?: 'Geschikt voor dagelijks salongebruik.' }}" disabled>
                </label>
            </div>

            <p class="product-required-note">Velden met een <span>*</span> zijn verplicht.</p>

            <div class="product-actions">
                <button class="product-primary-button" type="submit">Opslaan</button>
                <a class="product-reset-button" href="{{ route('behandelingen.producten.show', [$behandeling->Id, $product->Id]) }}">Terug</a>
            </div>
        </form>
    </section>
</main>

@include('components.site-footer')
