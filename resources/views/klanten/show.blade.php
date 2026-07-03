@include('components.site-navbar')

<style>
    .klant-detail-page {
        margin-top: 35px;
    }

    .klant-detail-breadcrumb {
        display: flex;
        gap: 8px;
        align-items: center;
        margin: 0 0 16px;
        color: #777f89;
        font-size: 13px;
        font-weight: 700;
    }

    .klant-detail-breadcrumb a {
        color: #d40a2f;
        text-decoration: none;
    }

    .klant-detail-panel {
        max-width: 760px;
        padding: 22px;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 12px 24px rgb(20 25 35 / 0.08);
    }

    .klant-detail-panel h1 {
        margin: 0 0 6px;
        color: #d40a2f;
        font-size: 23px;
        line-height: 1.2;
    }

    .klant-detail-panel p {
        margin: 0;
        color: #737d8a;
        font-weight: 700;
    }

    .klant-detail-list {
        display: grid;
        grid-template-columns: 190px minmax(0, 1fr);
        gap: 12px 18px;
        margin: 22px 0;
    }

    .klant-detail-list dt {
        color: #3b4149;
        font-weight: 800;
    }

    .klant-detail-list dd {
        margin: 0;
        color: #20242a;
        overflow-wrap: anywhere;
    }

    .klant-detail-back {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        justify-content: center;
        padding: 0 14px;
        border: 1px solid #2f8be6;
        border-radius: 6px;
        color: #1476d8;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
    }

    @media (max-width: 620px) {
        .klant-detail-list {
            grid-template-columns: 1fr;
            gap: 6px;
        }
    }
</style>

<main class="page-shell klant-detail-page">
    <nav class="klant-detail-breadcrumb" aria-label="Kruimelpad">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <a href="{{ route('klanten.index') }}">Klanten</a>
        <span>/</span>
        <span>Details</span>
    </nav>

    <section class="klant-detail-panel" aria-label="Klantdetails">
        <h1>{{ trim($klant->voornaam . ' ' . ($klant->tussenvoegsel ? $klant->tussenvoegsel . ' ' : '') . $klant->achternaam) }}</h1>
        <p>{{ $klant->relatienummer }}</p>

        <dl class="klant-detail-list">
            <dt>Adres</dt>
            <dd>{{ $klant->adres }}</dd>

            <dt>Postcode</dt>
            <dd>{{ $klant->postcode }}</dd>

            <dt>Woonplaats</dt>
            <dd>{{ $klant->woonplaats }}</dd>

            <dt>Mobiel</dt>
            <dd>{{ $klant->mobiel }}</dd>

            <dt>Contact e-mail</dt>
            <dd>{{ $klant->email }}</dd>

            <dt>Bijzonderheden</dt>
            <dd>{{ $klant->bijzonderheden ?: 'Geen bijzonderheden bekend.' }}</dd>
        </dl>

        <a class="klant-detail-back" href="{{ route('klanten.index') }}">Terug naar klanten</a>
    </section>
</main>

@include('components.site-footer')
