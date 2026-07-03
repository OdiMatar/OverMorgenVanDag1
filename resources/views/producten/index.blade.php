@include('components.site-navbar')

@php
    $geld = fn ($waarde) => $waarde === null ? '-' : 'EUR '.number_format((float) $waarde, 2, ',', '.');
@endphp

<style>
    .product-page { max-width: 1180px; margin: 44px auto 0; padding: 0 24px; }
    .product-breadcrumb { margin-bottom: 22px; color: #7b8492; font-weight: 700; }
    .product-breadcrumb a { color: #c9002b; text-decoration: none; }
    .product-title { margin: 0 0 12px; color: #c9002b; font-size: 22px; }
    .product-card { border-radius: 12px; background: #fff; box-shadow: 0 18px 35px rgb(25 30 40 / 0.08); }
    .product-filter { display: grid; grid-template-columns: 1fr auto auto; gap: 14px; align-items: end; min-height: 78px; padding: 14px; }
    .product-filter__field { max-width: 280px; margin-left: auto; }
    .product-label { display: block; margin-bottom: 6px; color: #27313c; font-size: 12px; font-weight: 800; }
    .product-select, .product-input { width: 100%; min-height: 34px; padding: 7px 10px; border: 1px solid #cfd6df; border-radius: 7px; background: #fff; color: #384454; font: inherit; }
    .product-btn { display: inline-flex; min-height: 34px; align-items: center; justify-content: center; padding: 0 20px; border: 0; border-radius: 7px; font-weight: 800; text-decoration: none; cursor: pointer; }
    .product-btn--red { background: #c9002b; color: #fff; }
    .product-btn--grey { background: #7d8a99; color: #fff; }
    .product-btn--outline { border: 1px solid #1f7bd5; background: #fff; color: #1f7bd5; }
    .product-table-wrap { margin-top: 16px; overflow: hidden; }
    .product-table-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px; color: #7b8492; font-size: 13px; }
    .product-pagination { display: flex; gap: 7px; justify-content: center; flex: 1; }
    .product-pagination a, .product-pagination span { display: inline-flex; width: 31px; height: 31px; align-items: center; justify-content: center; border: 1px solid #e1e6ed; border-radius: 7px; color: #c9002b; font-weight: 700; text-decoration: none; }
    .product-pagination .is-active { border-color: #c9002b; background: #c9002b; color: #fff; }
    .product-pagination .is-disabled { color: #cbd2dc; }
    .product-table { width: calc(100% - 20px); margin: 0 10px 10px; border-collapse: collapse; }
    .product-table th { padding: 12px; background: #c9002b; color: #fff; text-align: left; }
    .product-table td { padding: 10px 12px; border-bottom: 1px solid #e4e8ee; color: #384454; }
    .product-empty { padding: 26px; text-align: center; color: #384454; }
    @media (max-width: 760px) { .product-filter { grid-template-columns: 1fr; } .product-filter__field { max-width: none; margin-left: 0; } .product-table { min-width: 760px; } }
</style>

<main class="product-page">
    <div class="product-breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <span>Producten</span>
    </div>

    <h1 class="product-title">Overzicht producten</h1>

    <form class="product-card product-filter" method="GET" action="{{ route('producten.index') }}">
        <div class="product-filter__field">
            <label class="product-label" for="categorie_id">Categorie selecteren</label>
            <select class="product-select" id="categorie_id" name="categorie_id">
                <option value="">Alle categorieen</option>
                @foreach ($categorieen as $categorie)
                    <option value="{{ $categorie->Id }}" @selected($geselecteerdeCategorie === (int) $categorie->Id)>
                        {{ $categorie->Naam }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="product-btn product-btn--red" type="submit">Maak selectie</button>
        <a class="product-btn product-btn--grey" href="{{ route('producten.index') }}">Reset</a>
    </form>

    <section class="product-card product-table-wrap">
        <div class="product-table-head">
            <span>Gevonden producten - {{ $producten->total() }} product(en)</span>

            <nav class="product-pagination" aria-label="Product paginering">
                @if ($producten->onFirstPage())
                    <span class="is-disabled">&lsaquo;</span>
                @else
                    <a href="{{ $producten->previousPageUrl() }}">&lsaquo;</a>
                @endif

                @for ($pagina = 1; $pagina <= $producten->lastPage(); $pagina++)
                    @if ($pagina === $producten->currentPage())
                        <span class="is-active">{{ $pagina }}</span>
                    @else
                        <a href="{{ $producten->url($pagina) }}">{{ $pagina }}</a>
                    @endif
                @endfor

                @if ($producten->hasMorePages())
                    <a href="{{ $producten->nextPageUrl() }}">&rsaquo;</a>
                @else
                    <span class="is-disabled">&rsaquo;</span>
                @endif
            </nav>
        </div>

        <div style="overflow-x: auto;">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Categorie</th>
                        <th>Merk</th>
                        <th>EAN-code</th>
                        <th>Verkoopprijs</th>
                        <th>Voorraad</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($producten as $product)
                        <tr>
                            <td>{{ $product->Naam }}</td>
                            <td>{{ $product->CategorieNaam }}</td>
                            <td>{{ $product->Merk }}</td>
                            <td>{{ $product->EANcode }}</td>
                            <td>{{ $geld($product->VerkoopPrijs) }}</td>
                            <td>{{ $product->AantalOpVoorraad ?? '-' }}</td>
                            <td><a class="product-btn product-btn--outline" href="{{ route('producten.show', $product->Id) }}">Details</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td class="product-empty" colspan="7">Er zijn geen producten bekend binnen de geselecteerde categorie</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

@include('components.site-footer')
