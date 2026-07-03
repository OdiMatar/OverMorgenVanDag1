<style>
    body {
        margin: 0;
        min-width: 320px;
        background: #f5f6fa;
        color: #20242a;
        font-family: Arial, Helvetica, sans-serif;
    }

    .site-navbar {
        background: #d40a2f;
        color: #fff;
    }

    .site-navbar__inner {
        display: flex;
        min-height: 53px;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        max-width: 1120px;
        margin: 0 auto;
        padding: 0 18px;
    }

    .site-navbar__brand {
        color: #fff;
        font-size: 22px;
        font-weight: 900;
        letter-spacing: 1px;
        text-decoration: none;
        text-transform: uppercase;
    }

    .site-navbar__links {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 0;
        font-size: 12px;
        font-weight: 700;
    }

    .site-navbar__links a,
    .site-navbar__button {
        color: #fff;
        font: inherit;
        text-decoration: none;
    }

    .site-navbar__links > a:not(.site-navbar__button) {
        display: inline-flex;
        min-height: 30px;
        align-items: center;
        padding: 0 7px;
        border-radius: 6px;
    }

    .site-navbar__links > a.is-active {
        background: #9d3b70;
    }

    .site-navbar__button {
        display: inline-flex;
        min-height: 30px;
        align-items: center;
        justify-content: center;
        padding: 0 13px;
        border: 1px solid rgb(255 255 255 / 0.45);
        border-radius: 7px;
        background: transparent;
        cursor: pointer;
    }

    .site-navbar__links a.is-active {
        padding: 11px 9px;
        border-radius: 7px;
        background: #9f3155;
    }

    .site-navbar__user {
        margin: 0 9px 0 18px;
        font-size: 12px;
        font-weight: 400;
    }

    .page-shell {
        max-width: 900px;
        min-height: calc(100vh - 128px);
        margin: 49px auto 0;
        padding: 0 18px;
    }

    .site-footer {
        margin: 62px 0 0;
        padding: 0 24px 18px;
        color: #7d8590;
        font-size: 12px;
        text-align: center;
    }

    .behandelingen-breadcrumb {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-bottom: 19px;
        color: #758094;
        font-size: 13px;
        font-weight: 700;
    }

    .behandelingen-breadcrumb a {
        color: #d40a2f;
        text-decoration: none;
    }

    .behandelingen-title {
        margin: 0 0 13px;
        color: #d40a2f;
        font-size: 22px;
        line-height: 1.2;
    }

    .behandelingen-title span {
        color: #6f7886;
    }

    .behandelingen-filter-card,
    .behandelingen-table-card {
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 22px 35px rgb(35 45 65 / 0.08);
    }

    .behandelingen-filter-card {
        min-height: 70px;
        margin-bottom: 12px;
        padding: 12px 13px;
    }

    .behandelingen-filter-form {
        width: min(100%, 424px);
        margin-left: auto;
    }

    .behandelingen-filter-form label {
        display: block;
        margin-bottom: 6px;
        color: #202b3f;
        font-size: 12px;
        font-weight: 700;
    }

    .behandelingen-filter-row {
        display: grid;
        grid-template-columns: minmax(160px, 1fr) 116px 64px;
        gap: 8px;
        align-items: center;
    }

    .behandelingen-filter-row select {
        width: 100%;
        min-height: 32px;
        padding: 0 9px;
        border: 1px solid #d6dde6;
        border-radius: 7px;
        background: #fff;
        color: #303845;
        font-size: 13px;
    }

    .behandelingen-primary-button,
    .behandelingen-reset-button {
        display: inline-flex;
        min-height: 32px;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }

    .behandelingen-primary-button {
        border: 0;
        background: #d40a2f;
        color: #fff;
        cursor: pointer;
    }

    .behandelingen-reset-button {
        background: #737f8d;
        color: #fff;
    }

    .behandelingen-table-card {
        overflow: hidden;
        padding: 12px 8px 8px;
    }

    .behandelingen-count {
        margin: 0 0 19px 4px;
        color: #8692a5;
        font-size: 13px;
    }

    .behandelingen-pagination {
        display: flex;
        justify-content: center;
        gap: 6px;
        margin-bottom: 12px;
    }

    .behandelingen-page-link {
        display: inline-grid;
        min-width: 30px;
        height: 30px;
        place-items: center;
        border: 1px solid #dde5ef;
        border-radius: 7px;
        color: #d40a2f;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }

    .behandelingen-page-link.is-active {
        border-color: #d40a2f;
        background: #d40a2f;
        color: #fff;
    }

    .behandelingen-page-link.is-disabled {
        color: #cbd3dd;
    }

    .behandelingen-table-wrap {
        overflow-x: auto;
    }

    .behandelingen-table {
        width: 100%;
        border-collapse: collapse;
        color: #202b3f;
        font-size: 13px;
    }

    .behandelingen-table thead {
        background: #d40a2f;
        color: #fff;
    }

    .behandelingen-table th,
    .behandelingen-table td {
        padding: 9px 10px;
        border-bottom: 1px solid #e5e9ef;
        text-align: left;
        vertical-align: middle;
    }

    .behandelingen-table th:nth-child(3),
    .behandelingen-table td:nth-child(3),
    .behandelingen-table th:nth-child(4),
    .behandelingen-table td:nth-child(4),
    .behandelingen-table th:nth-child(5),
    .behandelingen-table td:nth-child(5),
    .behandelingen-table th:nth-child(6),
    .behandelingen-table td:nth-child(6) {
        white-space: nowrap;
    }

    .behandelingen-table td:nth-child(4) {
        font-weight: 800;
    }

    .behandelingen-product-button {
        display: inline-flex;
        min-width: 83px;
        min-height: 29px;
        align-items: center;
        justify-content: center;
        border: 1px solid #1e78ff;
        border-radius: 7px;
        color: #1e78ff;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }

    .behandelingen-empty-message {
        height: 46px;
        color: #202b3f;
        text-align: center !important;
    }

    .product-panel,
    .product-detail-card,
    .product-form-card {
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 22px 35px rgb(35 45 65 / 0.08);
    }

    .product-panel {
        overflow: hidden;
        padding: 0 10px 8px;
    }

    .product-table {
        width: 100%;
        border-collapse: collapse;
        color: #111827;
        font-size: 13px;
    }

    .product-table th,
    .product-table td {
        padding: 10px 8px;
        border-bottom: 1px solid #e5e9ef;
        text-align: left;
        vertical-align: middle;
    }

    .product-table th {
        font-weight: 800;
    }

    .product-table td:nth-child(4),
    .product-table td:nth-child(5),
    .product-table td:nth-child(6),
    .product-table td:nth-child(7) {
        white-space: nowrap;
    }

    .product-primary-button,
    .product-secondary-button,
    .product-reset-button {
        display: inline-flex;
        min-width: 73px;
        min-height: 29px;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    .product-primary-button {
        border: 1px solid #d40a2f;
        background: #d40a2f;
        color: #fff;
    }

    .product-secondary-button {
        border: 1px solid #1e78ff;
        background: #fff;
        color: #1e78ff;
    }

    .product-reset-button {
        border: 1px solid #737f8d;
        background: #737f8d;
        color: #fff;
    }

    .product-detail-card {
        width: min(100%, 620px);
        padding: 10px 11px 9px;
    }

    .product-detail-list {
        margin: 0;
    }

    .product-detail-list div {
        display: grid;
        grid-template-columns: 150px minmax(0, 1fr);
        gap: 6px;
        min-height: 29px;
        align-items: center;
        border-bottom: 1px solid #e5e9ef;
    }

    .product-detail-list dt,
    .product-detail-list dd {
        margin: 0;
        font-size: 13px;
    }

    .product-detail-list dt {
        font-weight: 800;
    }

    .product-actions {
        display: flex;
        justify-content: flex-end;
        gap: 7px;
        margin-top: 12px;
    }

    .product-form-card {
        width: min(100%, 760px);
        padding: 18px 18px 8px;
    }

    .product-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px 14px;
    }

    .product-form-grid label {
        display: grid;
        gap: 6px;
        color: #263244;
        font-size: 12px;
        font-weight: 800;
    }

    .product-form-grid label span,
    .product-required-note span {
        color: #d40a2f;
    }

    .product-form-grid input {
        width: 100%;
        min-height: 32px;
        box-sizing: border-box;
        padding: 0 9px;
        border: 1px solid #d6dde6;
        border-radius: 7px;
        background: #fff;
        color: #344054;
        font-size: 13px;
    }

    .product-form-grid input:disabled {
        background: #f7f8fa;
        color: #8490a4;
    }

    .product-form-grid input.is-invalid {
        border-color: #ff4658;
        color: #d40a2f;
    }

    .product-form-grid small {
        color: #6f7886;
        font-size: 12px;
        font-weight: 400;
    }

    .product-field-error {
        color: #ff4658;
        font-size: 12px;
        font-weight: 400;
    }

    .product-required-note {
        margin: 25px 0 14px;
        color: #718096;
        font-size: 12px;
    }

    .product-alert {
        width: min(100%, 620px);
        min-height: 49px;
        box-sizing: border-box;
        margin-bottom: 18px;
        padding: 16px 14px;
        border-radius: 5px;
        font-size: 14px;
    }

    .product-alert-success {
        border: 1px solid #a8d8c0;
        background: #d5eee0;
        color: #145238;
    }

    .product-alert-error {
        border: 1px solid #f3a3ad;
        background: #f9d4d9;
        color: #7e2230;
    }

    .product-empty {
        margin: 0;
        padding: 18px;
        color: #718096;
        font-size: 13px;
    }

    @media (max-width: 760px) {
        .site-navbar__inner {
            align-items: flex-start;
            flex-direction: column;
            padding-block: 14px;
        }

        .site-navbar__links {
            justify-content: flex-start;
        }

        .product-form-grid,
        .product-detail-list div {
            grid-template-columns: 1fr;
        }
    }
</style>

<header class="site-navbar">
    <div class="site-navbar__inner">
        <a class="site-navbar__brand" href="{{ route('home') }}">Kniploket Tiko</a>

        <nav class="site-navbar__links" aria-label="Hoofdnavigatie">
            @auth
                @if (auth()->user()->isEigenaar())
                    <a href="#">Accounts</a>
                    <a href="{{ route('medewerkers.index') }}">Medewerkers</a>
                    <a href="#">Beschikbaarheid</a>
                    <a href="{{ route('klanten.index') }}">Klanten</a>
                    <a href="#">Afspraken</a>
                    <a href="{{ route('behandelingen.index') }}">Behandelingen</a>
                    <a class="{{ request()->is('producten*') ? 'is-active' : '' }}" href="{{ route('producten.index') }}">Producten</a>
                    <a href="#">Bestellingen</a>
                @endif

                <span class="site-navbar__user">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                <a class="site-navbar__button" href="{{ route('logout') }}">Uitloggen</a>
            @else
                <a href="{{ route('login') }}">Inloggen</a>
                <a class="site-navbar__button" href="{{ route('register') }}">Account aanmaken</a>
            @endauth
        </nav>
    </div>
</header>
