@include('components.site-navbar')

<main class="page-shell behandelingen-page">
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
            <div>
                <dt>Product</dt>
                <dd>{{ $product->Naam }}</dd>
            </div>
            <div>
                <dt>Merk</dt>
                <dd>{{ $product->Merk }}</dd>
            </div>
            <div>
                <dt>Omschrijving</dt>
                <dd>{{ $product->Omschrijving }}</dd>
            </div>
            <div>
                <dt>EAN-code</dt>
                <dd>{{ $product->EANcode }}</dd>
            </div>
            <div>
                <dt>Houdbaarheidsdatum</dt>
                <dd>{{ \Carbon\Carbon::parse($product->Houdbaarheidsdatum)->format('d-m-Y') }}</dd>
            </div>
            <div>
                <dt>Inkoopprijs</dt>
                <dd>EUR {{ number_format((float) $product->InkoopPrijs, 2, ',', '.') }}</dd>
            </div>
            <div>
                <dt>Verkoopprijs</dt>
                <dd>EUR {{ number_format((float) $product->VerkoopPrijs, 2, ',', '.') }}</dd>
            </div>
            <div>
                <dt>Aantal op voorraad</dt>
                <dd>{{ $product->AantalOpVoorraad }}</dd>
            </div>
            <div>
                <dt>Leverancier</dt>
                <dd>{{ $product->LeverancierNaam ?? 'Onbekend' }}</dd>
            </div>
            <div>
                <dt>Postcode leverancier</dt>
                <dd>{{ $product->LeverancierPostcode ?? '-' }}</dd>
            </div>
            <div>
                <dt>Plaats leverancier</dt>
                <dd>{{ $product->LeverancierPlaats ?? '-' }}</dd>
            </div>
            <div>
                <dt>E-mail leverancier</dt>
                <dd>{{ $product->LeverancierEmail ?? '-' }}</dd>
            </div>
            <div>
                <dt>Mobiel leverancier</dt>
                <dd>{{ $product->LeverancierMobiel ?? '-' }}</dd>
            </div>
            <div>
                <dt>Opmerking</dt>
                <dd>{{ $product->Opmerking ?: 'Geschikt voor dagelijks salongebruik.' }}</dd>
            </div>
        </dl>

        <div class="product-actions">
            <a class="product-primary-button" href="{{ route('behandelingen.producten.edit', [$behandeling->Id, $product->Id]) }}">Wijzigen</a>
            <a class="product-secondary-button" href="{{ route('behandelingen.producten.index', $behandeling->Id) }}">Terug</a>
        </div>
    </section>
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
