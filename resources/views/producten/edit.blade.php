@include('components.site-navbar')

@php
    $leeg = fn ($waarde) => filled($waarde) ? $waarde : '-';
    $geld = fn ($waarde) => $waarde === null ? '-' : 'EUR '.number_format((float) $waarde, 2, ',', '.');
    $datum = fn ($waarde) => $waarde ? \Carbon\Carbon::parse($waarde)->format('d-m-Y') : '-';
    $datumInput = old('houdbaarheidsdatum', \Carbon\Carbon::parse($product->Houdbaarheidsdatum)->format('Y-m-d'));
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp

<style>
    .product-page { max-width: 1180px; margin: 44px auto 0; padding: 0 24px; }
    .product-breadcrumb { margin-bottom: 22px; color: #7b8492; font-weight: 700; }
    .product-breadcrumb a { color: #c9002b; text-decoration: none; }
    .product-title { margin: 0 0 12px; color: #c9002b; font-size: 22px; }
    .product-title span { color: #6c7683; }
    .product-card { width: min(100%, 790px); padding: 22px; border-radius: 12px; background: #fff; box-shadow: 0 18px 35px rgb(25 30 40 / 0.08); }
    .product-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 17px 14px; }
    .product-field label { display: block; margin-bottom: 7px; color: #27313c; font-size: 12px; font-weight: 800; }
    .product-input { width: 100%; min-height: 34px; padding: 7px 10px; border: 1px solid #cfd6df; border-radius: 7px; color: #384454; font: inherit; }
    .product-input[readonly] { background: #f7f8fa; color: #7b8492; }
    .product-input.is-invalid { border-color: #d64555; }
    .product-help { margin-top: 7px; color: #7b8492; font-size: 13px; }
    .product-error { margin-top: 7px; color: #c9002b; font-size: 13px; }
    .product-required { margin-top: 28px; color: #7b8492; font-size: 13px; }
    .product-required span { color: #c9002b; font-weight: 800; }
    .product-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
    .product-btn { display: inline-flex; min-height: 34px; align-items: center; justify-content: center; padding: 0 16px; border: 0; border-radius: 7px; font: inherit; font-weight: 800; text-decoration: none; cursor: pointer; }
    .product-btn--red { background: #c9002b; color: #fff; }
    .product-btn--grey { background: #7d8a99; color: #fff; }
    .product-alert { width: min(100%, 650px); margin-bottom: 18px; padding: 18px; border-radius: 5px; background: #f7d4d9; color: #733040; border: 1px solid #e7aeb8; }
    @media (max-width: 760px) { .product-form-grid { grid-template-columns: 1fr; } }
</style>

<main class="product-page">
    @if (session('error'))
        <div class="product-alert">{{ session('error') }}</div>
    @endif

    <div class="product-breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <a href="{{ route('producten.index') }}">Producten</a>
        <span>/</span>
        <span>Wijzigen</span>
    </div>

    <h1 class="product-title">Product wijzigen <span>{{ $product->Naam }}</span></h1>

    <form class="product-card" method="POST" action="{{ route('producten.update', $product->Id) }}">
        @csrf
        @method('PUT')

        <div class="product-form-grid">
            <div class="product-field">
                <label>Product</label>
                <input class="product-input" value="{{ $product->Naam }}" readonly>
            </div>

            <div class="product-field">
                <label>Merk</label>
                <input class="product-input" value="{{ $product->Merk }}" readonly>
            </div>

            <div class="product-field">
                <label>Omschrijving</label>
                <input class="product-input" value="{{ $product->Omschrijving }}" readonly>
            </div>

            <div class="product-field">
                <label>EAN-code</label>
                <input class="product-input" value="{{ $product->EANcode }}" readonly>
            </div>

            <div class="product-field">
                <label>Inkoopprijs</label>
                <input class="product-input" value="{{ $geld($product->InkoopPrijs) }}" readonly>
            </div>

            <div class="product-field">
                <label>Aantal op voorraad</label>
                <input class="product-input" value="{{ $leeg($product->AantalOpVoorraad) }}" readonly>
            </div>

            <div class="product-field">
                <label>Huidige verkoopprijs</label>
                <input class="product-input" value="{{ $geld($product->VerkoopPrijs) }}" readonly>
            </div>

            <div class="product-field">
                <label>Leverancier</label>
                <input class="product-input" value="{{ $leeg($product->LeverancierNaam) }}" readonly>
            </div>

            <div class="product-field">
                <label>Houdbaarheidsdatum</label>
                <input class="product-input" value="{{ $datum($product->Houdbaarheidsdatum) }}" readonly>
            </div>

            <div class="product-field">
                <label>Plaats leverancier</label>
                <input class="product-input" value="{{ $leeg($product->LeverancierPlaats) }}" readonly>
            </div>

            <div class="product-field">
                <label for="houdbaarheidsdatum">Nieuwe houdbaarheidsdatum <span style="color: #c9002b;">*</span></label>
                <input
                    class="product-input @error('houdbaarheidsdatum') is-invalid @enderror"
                    id="houdbaarheidsdatum"
                    name="houdbaarheidsdatum"
                    type="date"
                    value="{{ $datumInput }}"
                    required
                >
                @error('houdbaarheidsdatum')
                    <div class="product-error">{{ $message }}</div>
                @enderror
                <div class="product-help">De houdbaarheidsdatum mag uiterlijk met 7 dagen worden verlengd.</div>
            </div>

            <div class="product-field">
                <label>Opmerking</label>
                <input class="product-input" value="{{ $leeg($product->Opmerking) }}" readonly>
            </div>
        </div>

        <p class="product-required">Velden met een <span>*</span> zijn verplicht.</p>

        <div class="product-actions">
            <button class="product-btn product-btn--red" type="submit">Opslaan</button>
            <a class="product-btn product-btn--grey" href="{{ route('producten.show', $product->Id) }}">Terug</a>
        </div>
    </form>
</main>

@include('components.site-footer')
