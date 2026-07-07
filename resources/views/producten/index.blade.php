@include('components.site-navbar')

<style>
    .producten-page {
        max-width: 1120px;
        margin: 44px auto 0;
        padding: 0 24px;
    }

    .producten-breadcrumb {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-bottom: 22px;
        color: #7b8492;
        font-size: 13px;
        font-weight: 700;
    }

    .producten-breadcrumb a {
        color: #c9002b;
        text-decoration: none;
    }

    .producten-title {
        margin: 0 0 14px;
        color: #c9002b;
        font-size: 22px;
    }

    .producten-filter-card,
    .producten-card {
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 18px 35px rgb(25 30 40 / 0.08);
    }

    .producten-filter-card {
        margin-bottom: 12px;
        padding: 14px;
    }

    .producten-filter-form {
        display: grid;
        grid-template-columns: minmax(180px, 280px) auto auto;
        gap: 8px;
        align-items: end;
        margin-left: auto;
        width: fit-content;
    }

    .producten-filter-form label {
        display: grid;
        gap: 6px;
        color: #202b3f;
        font-size: 12px;
        font-weight: 800;
    }

    .producten-filter-form select {
        min-height: 34px;
        padding: 0 9px;
        border: 1px solid #d6dde6;
        border-radius: 7px;
        background: #fff;
    }

    .producten-btn {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        justify-content: center;
        padding: 0 14px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    .producten-btn--red {
        border: 1px solid #c9002b;
        background: #c9002b;
        color: #fff;
    }

    .producten-btn--gray {
        border: 1px solid #737f8d;
        background: #737f8d;
        color: #fff;
    }

    .producten-btn--outline {
        border: 1px solid #1f7bd5;
        background: #fff;
        color: #1f7bd5;
    }

    .producten-card {
        overflow: hidden;
        padding: 10px;
    }

    .producten-count {
        margin: 0 0 12px 4px;
        color: #8692a5;
        font-size: 13px;
    }

    .producten-table-wrap {
        overflow-x: auto;
    }

    .producten-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .producten-table th,
    .producten-table td {
        padding: 10px 9px;
        border-bottom: 1px solid #e5e9ef;
        text-align: left;
        vertical-align: middle;
    }

    .producten-table thead {
        background: #c9002b;
        color: #fff;
    }

    .producten-pagination {
        display: flex;
        justify-content: center;
        gap: 6px;
        margin: 14px 0 6px;
    }

    .producten-pagination a,
    .producten-pagination span {
        display: inline-grid;
        min-width: 30px;
        height: 30px;
        place-items: center;
        border: 1px solid #dde5ef;
        border-radius: 7px;
        color: #c9002b;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }

    .producten-pagination .is-active {
        border-color: #c9002b;
        background: #c9002b;
        color: #fff;
    }

    @media (max-width: 760px) {
        .producten-filter-form {
            width: 100%;
            grid-template-columns: 1fr;
        }
    }
</style>

<main class="producten-page">
    <nav class="producten-breadcrumb" aria-label="Kruimelpad">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <span>Producten</span>
    </nav>

    <h1 class="producten-title">Overzicht producten</h1>

    <section class="producten-filter-card" aria-label="Producten filteren">
        <form class="producten-filter-form" method="GET" action="{{ route('producten.index') }}">
            <label for="categorie_id">
                Categorie selecteren
                <select id="categorie_id" name="categorie_id">
                    <option value="">Alle categorieen</option>
                    @foreach ($categorieen as $categorie)
                        <option value="{{ $categorie->Id }}" @selected((int) $geselecteerdeCategorie === (int) $categorie->Id)>
                            {{ $categorie->Naam }}
                        </option>
                    @endforeach
                </select>
            </label>

            <button class="producten-btn producten-btn--red" type="submit">Maak selectie</button>
            <a class="producten-btn producten-btn--gray" href="{{ route('producten.index') }}">Reset</a>
        </form>
    </section>

    <section class="producten-card">
        <p class="producten-count">Gevonden producten - {{ $producten->total() }} product(en)</p>

        @if ($producten->hasPages())
            <div class="producten-pagination">
                @if ($producten->onFirstPage())
                    <span>&lsaquo;</span>
                @else
                    <a href="{{ $producten->previousPageUrl() }}" aria-label="Vorige pagina">&lsaquo;</a>
                @endif

                @for ($pagina = 1; $pagina <= $producten->lastPage(); $pagina++)
                    @if ($pagina === $producten->currentPage())
                        <span class="is-active">{{ $pagina }}</span>
                    @else
                        <a href="{{ $producten->url($pagina) }}">{{ $pagina }}</a>
                    @endif
                @endfor

                @if ($producten->hasMorePages())
                    <a href="{{ $producten->nextPageUrl() }}" aria-label="Volgende pagina">&rsaquo;</a>
                @else
                    <span>&rsaquo;</span>
                @endif
            </div>
        @endif

        <div class="producten-table-wrap">
            <table class="producten-table">
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
                            <td>EUR {{ number_format((float) $product->VerkoopPrijs, 2, ',', '.') }}</td>
                            <td>{{ $product->AantalOpVoorraad ?? 0 }}</td>
                            <td>
                                <a class="producten-btn producten-btn--outline" href="{{ route('producten.show', $product->Id) }}">Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">Er zijn geen producten beschikbaar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

@include('components.site-footer')
