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
        max-width: 1180px;
        margin: 0 auto;
        padding: 0 24px;
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
        gap: 16px;
        font-size: 13px;
        font-weight: 700;
    }

    .site-navbar__links a,
    .site-navbar__button {
        color: #fff;
        font: inherit;
        text-decoration: none;
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

    .site-navbar__user {
        font-size: 12px;
        font-weight: 400;
    }

    .page-shell {
        max-width: 1180px;
        min-height: calc(100vh - 118px);
        margin: 42px auto 0;
        padding: 0 24px;
    }

    .site-footer {
        margin: 45px 0 0;
        padding: 0 24px 18px;
        color: #7d8590;
        font-size: 12px;
        text-align: center;
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
    }
</style>

<header class="site-navbar">
    <div class="site-navbar__inner">
        <a class="site-navbar__brand" href="{{ route('home') }}">Kniploket Tiko</a>

        <nav class="site-navbar__links" aria-label="Hoofdnavigatie">
            @auth
                @if (auth()->user()->isEigenaar())
                    <a href="{{ route('klanten.index') }}">Klanten</a>
                    <a href="{{ route('medewerkers.index') }}">Medewerkers</a>
                    <a href="{{ route('behandelingen.index') }}">Behandelingen</a>
                    <a href="{{ route('producten.index') }}">Producten</a>
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
