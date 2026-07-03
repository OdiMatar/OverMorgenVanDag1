@include('components.site-navbar')

<main class="page-shell behandelingen-page">
    @isset($behandeling)
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
                        @foreach ($producten as $product)
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
                        @endforeach
                        <tr>
                            <td colspan="6"></td>
                            <td>
                                <a class="product-secondary-button" href="{{ route('behandelingen.index') }}">Terug</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    @else
        <nav class="behandelingen-breadcrumb" aria-label="Kruimelpad">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <span>Producten</span>
        </nav>

        <h1 class="behandelingen-title">Producten</h1>
        <section class="product-panel">
            <p class="product-empty">Selecteer eerst een behandeling via het overzicht behandelingen.</p>
        </section>
    @endisset
</main>

@include('components.site-footer')
