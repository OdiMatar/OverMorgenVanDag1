@include('components.site-navbar')

@php
    // De date-input gebruikt Y-m-d; gewone tekst gebruikt d-m-Y.
    $datum = fn ($waarde) => $waarde ? \Carbon\Carbon::parse($waarde)->format('Y-m-d') : '';
    $datumNl = fn ($waarde) => $waarde ? \Carbon\Carbon::parse($waarde)->format('d-m-Y') : '-';
    $geld = fn ($waarde) => $waarde === null ? '-' : 'EUR '.number_format((float) $waarde, 2, ',', '.');
@endphp

<style>
    .product-edit-page { max-width: 900px; margin: 44px auto 0; padding: 0 24px; }
    .product-breadcrumb { display: flex; gap: 8px; margin-bottom: 22px; color: #7b8492; font-size: 13px; font-weight: 700; }
    .product-breadcrumb a { color: #c9002b; text-decoration: none; }
    .product-title { margin: 0 0 12px; color: #c9002b; font-size: 22px; }
    .product-title span { color: #6c7683; }
    .product-form-card { width: min(100%, 760px); padding: 18px; border-radius: 12px; background: #fff; box-shadow: 0 18px 35px rgb(25 30 40 / 0.08); }
    .product-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 14px; }
    .product-form-grid label { display: grid; gap: 6px; color: #263244; font-size: 12px; font-weight: 800; }
    .product-form-grid input { min-height: 34px; box-sizing: border-box; padding: 0 9px; border: 1px solid #d6dde6; border-radius: 7px; color: #344054; font-size: 13px; }
    .product-form-grid input:disabled { background: #f7f8fa; color: #8490a4; }
    .product-form-grid input.is-invalid { border-color: #ff4658; }
    .product-form-grid small { color: #6c7683; font-size: 12px; font-weight: 400; }
    .product-field-error { color: #ff4658; font-size: 12px; font-weight: 400; }
    .product-alert { width: min(100%, 760px); margin-bottom: 18px; padding: 16px; border-radius: 5px; background: #f9d4d9; color: #7e2230; border: 1px solid #f3a3ad; }
    .product-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; }
    .product-btn { display: inline-flex; min-height: 34px; align-items: center; justify-content: center; padding: 0 16px; border-radius: 7px; font-size: 12px; font-weight: 800; text-decoration: none; cursor: pointer; }
    .product-btn--red { border: 1px solid #c9002b; background: #c9002b; color: #fff; }
    .product-btn--gray { border: 1px solid #737f8d; background: #737f8d; color: #fff; }

    @media (max-width: 760px) {
        .product-form-grid { grid-template-columns: 1fr; }
    }
</style>

<main class="product-edit-page">
    @if (session('error'))
        <div class="product-alert">{{ session('error') }}</div>
    @endif

    <nav class="product-breadcrumb" aria-label="Kruimelpad">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <a href="{{ route('producten.index') }}">Producten</a>
        <span>/</span>
        <span>Wijzigen</span>
    </nav>

    <h1 class="product-title">Product wijzigen <span>{{ $product->Naam }}</span></h1>

    {{-- Alleen de houdbaarheidsdatum is aanpasbaar; de rest is context voor de gebruiker. --}}
    <section class="product-form-card">
        <form method="POST" action="{{ route('producten.update', $product->Id) }}">
            @csrf
            @method('PUT')

            <div class="product-form-grid">
                <label>Product <input type="text" value="{{ $product->Naam }}" disabled></label>
                <label>Merk <input type="text" value="{{ $product->Merk }}" disabled></label>
                <label>Omschrijving <input type="text" value="{{ $product->Omschrijving }}" disabled></label>
                <label>EAN-code <input type="text" value="{{ $product->EANcode }}" disabled></label>
                <label>Huidige houdbaarheidsdatum <input type="text" value="{{ $datumNl($product->Houdbaarheidsdatum) }}" disabled></label>
                <label>Aantal op voorraad <input type="text" value="{{ $product->AantalOpVoorraad ?? 0 }}" disabled></label>
                <label>Inkoopprijs <input type="text" value="{{ $geld($product->InkoopPrijs) }}" disabled></label>
                <label>Verkoopprijs <input type="text" value="{{ $geld($product->VerkoopPrijs) }}" disabled></label>
                <label>
                    Nieuwe houdbaarheidsdatum
                    <input
                        @class(['is-invalid' => isset($errors) && $errors->has('houdbaarheidsdatum')])
                        type="date"
                        name="houdbaarheidsdatum"
                        value="{{ old('houdbaarheidsdatum', $datum($product->Houdbaarheidsdatum)) }}"
                    >
                    @if (isset($errors) && $errors->has('houdbaarheidsdatum'))
                        <strong class="product-field-error">{{ $errors->first('houdbaarheidsdatum') }}</strong>
                    @endif
                    <small>De houdbaarheidsdatum mag uiterlijk met 7 dagen worden verlengd</small>
                </label>
                <label>Leverancier <input type="text" value="{{ $product->LeverancierNaam ?? 'Onbekend' }}" disabled></label>
            </div>

            <div class="product-actions">
                <button class="product-btn product-btn--red" type="submit">Opslaan</button>
                <a class="product-btn product-btn--gray" href="{{ route('producten.show', $product->Id) }}">Terug</a>
            </div>
        </form>
    </section>
</main>

@include('components.site-footer')
