@include('components.site-navbar')

@php
    // Kleine weergavehelpers voor lege waarden, geldbedragen en datums.
    $leeg = fn ($waarde) => filled($waarde) ? $waarde : '-';
    $geld = fn ($waarde) => $waarde === null ? '-' : 'EUR '.number_format((float) $waarde, 2, ',', '.');
    $datum = fn ($waarde) => $waarde ? \Carbon\Carbon::parse($waarde)->format('d-m-Y') : '-';
@endphp

<style>
    .product-page { max-width: 1180px; margin: 44px auto 0; padding: 0 24px; }
    .product-breadcrumb { margin-bottom: 22px; color: #7b8492; font-weight: 700; }
    .product-breadcrumb a { color: #c9002b; text-decoration: none; }
    .product-title { margin: 0 0 12px; color: #c9002b; font-size: 22px; }
    .product-title span { color: #6c7683; }
    .product-card { width: min(100%, 650px); padding: 16px 22px 12px; border-radius: 12px; background: #fff; box-shadow: 0 18px 35px rgb(25 30 40 / 0.08); }
    .product-detail-row { display: grid; grid-template-columns: minmax(150px, 0.42fr) minmax(0, 1fr); gap: 10px 16px; align-items: start; padding: 8px 0; border-bottom: 1px solid #e4e8ee; }
    .product-detail-row:last-of-type { border-bottom: 0; }
    .product-detail-row strong { min-width: 0; font-weight: 800; overflow-wrap: anywhere; }
    .product-detail-row span { min-width: 0; overflow-wrap: anywhere; word-break: break-word; }
    .product-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
    .product-btn { display: inline-flex; min-height: 34px; align-items: center; justify-content: center; padding: 0 16px; border-radius: 7px; font-weight: 800; text-decoration: none; }
    .product-btn--red { background: #c9002b; color: #fff; }
    .product-btn--outline { border: 1px solid #1f7bd5; background: #fff; color: #1f7bd5; }
    .product-alert { width: min(100%, 650px); margin-bottom: 18px; padding: 18px; border-radius: 5px; background: #d7eddf; color: #1d5b3a; border: 1px solid #afd8bf; }
    @media (max-width: 560px) {
        .product-page { padding: 0 16px; }
        .product-card { padding: 14px 16px 10px; }
        .product-detail-row { grid-template-columns: 1fr; gap: 3px; }
    }
</style>

<main class="product-page">
    @if (session('status'))
        <div class="product-alert" data-auto-dismiss>{{ session('status') }}</div>
    @endif

    <div class="product-breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <a href="{{ route('producten.index') }}">Producten</a>
        <span>/</span>
        <span>Detail</span>
    </div>

    <h1 class="product-title">Productdetail <span>{{ $product->Naam }}</span></h1>

    {{-- Toon alle productdetails als label-waarde regels. --}}
    <section class="product-card">
        @foreach ([
            'Product' => $product->Naam,
            'Merk' => $product->Merk,
            'Omschrijving' => $product->Omschrijving,
            'EAN-code' => $product->EANcode,
            'Houdbaarheidsdatum' => $datum($product->Houdbaarheidsdatum),
            'Inkoopprijs' => $geld($product->InkoopPrijs),
            'Verkoopprijs' => $geld($product->VerkoopPrijs),
            'Aantal op voorraad' => $product->AantalOpVoorraad,
            'Leverancier' => $product->LeverancierNaam,
            'Postcode leverancier' => $product->LeverancierPostcode,
            'Plaats leverancier' => $product->LeverancierPlaats,
            'E-mail leverancier' => $product->LeverancierEmail,
            'Mobiel leverancier' => $product->LeverancierMobiel,
            'Opmerking' => $product->Opmerking,
        ] as $label => $waarde)
            <div class="product-detail-row">
                <strong>{{ $label }}</strong>
                <span>{{ $leeg($waarde) }}</span>
            </div>
        @endforeach

        <div class="product-actions">
            <a class="product-btn product-btn--red" href="{{ route('producten.edit', $product->Id) }}">Wijzigen</a>
            <a class="product-btn product-btn--outline" href="{{ route('producten.index') }}">Terug</a>
        </div>
    </section>
</main>

<script>
    window.setTimeout(() => {
        document.querySelectorAll('[data-auto-dismiss]').forEach((element) => element.remove());
    }, 3000);
</script>

@include('components.site-footer')
