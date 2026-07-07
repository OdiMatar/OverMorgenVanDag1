@include('components.site-navbar')

<style>
    .afspraken-page {
        margin-top: 35px;
    }

    .afspraken-breadcrumb {
        display: flex;
        gap: 8px;
        align-items: center;
        margin: 0 0 16px;
        color: #777f89;
        font-size: 13px;
        font-weight: 700;
    }

    .afspraken-breadcrumb a {
        color: #d40a2f;
        text-decoration: none;
    }

    .afspraken-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .afspraken-heading h1 {
        margin: 0;
        color: #d40a2f;
        font-size: 22px;
        line-height: 1.2;
    }

    .afspraken-btn-create {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        justify-content: center;
        padding: 0 16px;
        border-radius: 6px;
        background: #d40a2f;
        color: #fff;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(212, 10, 47, 0.2);
        transition: background 0.2s;
    }

    .afspraken-btn-create:hover {
        background: #b30825;
    }

    .afspraken-section {
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 12px 24px rgb(20 25 35 / 0.08);
        padding: 20px;
        margin-bottom: 24px;
    }

    .afspraken-section h2 {
        margin: 0 0 16px;
        font-size: 18px;
        color: #20242a;
        border-bottom: 2px solid #edf0f4;
        padding-bottom: 8px;
    }

    .afspraken-table-wrap {
        overflow-x: auto;
    }

    .afspraken-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .afspraken-table th {
        background: #f7f8fa;
        color: #4a5568;
        font-weight: 800;
        text-align: left;
    }

    .afspraken-table th,
    .afspraken-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #edf0f4;
        vertical-align: middle;
    }

    .afspraken-table tbody tr:hover {
        background: #fafbfc;
    }

    /* Bug US1 Fix: Highly visible blue button for Edit */
    .afspraken-btn-edit {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        justify-content: center;
        padding: 0 12px;
        border: 1px solid #2f8be6;
        border-radius: 6px;
        background-color: #2f8be6;
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        margin-right: 6px;
        box-shadow: 0 2px 4px rgba(47, 139, 230, 0.2);
        transition: background-color 0.2s;
    }

    .afspraken-btn-edit:hover {
        background-color: #1a71c2;
    }

    /* Red button for Cancel */
    .afspraken-btn-cancel {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        justify-content: center;
        padding: 0 12px;
        border: 1px solid #e53e3e;
        border-radius: 6px;
        background-color: #fff;
        color: #e53e3e;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.2s, color 0.2s;
    }

    .afspraken-btn-cancel:hover {
        background-color: #e53e3e;
        color: #fff;
    }

    .afspraken-badge {
        display: inline-flex;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .afspraken-badge--inbehandeling {
        background: #ebf8ff;
        color: #2b6cb0;
    }

    .afspraken-badge--behandeld {
        background: #f0fff4;
        color: #38a169;
    }

    .afspraken-badge--geannuleerd {
        background: #fff5f5;
        color: #e53e3e;
    }

    .afspraken-empty {
        padding: 24px;
        color: #718096;
        text-align: center;
        font-weight: 600;
        font-size: 14px;
    }

    .afspraken-flash-success {
        box-sizing: border-box;
        margin: 0 0 18px;
        padding: 15px 16px;
        border: 1px solid #a8d8c0;
        border-radius: 5px;
        background: #d5eee0;
        color: #145238;
        font-size: 14px;
        font-weight: 700;
    }

    .afspraken-flash-error {
        box-sizing: border-box;
        margin: 0 0 18px;
        padding: 15px 16px;
        border: 1px solid #f3a3ad;
        border-radius: 5px;
        background: #f9d4d9;
        color: #7e2230;
        font-size: 14px;
        font-weight: 700;
    }

    /* Bug US3 Fix: Beautiful, Custom CSS modal confirmation box */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-card {
        background: #fff;
        border-radius: 12px;
        width: min(90%, 440px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        animation: modalFadeIn 0.3s;
    }

    @keyframes modalFadeIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        background: #d40a2f;
        color: #fff;
        padding: 16px 20px;
        font-size: 16px;
        font-weight: 800;
    }

    .modal-body {
        padding: 20px;
        font-size: 14px;
        color: #4a5568;
        line-height: 1.5;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 14px 20px;
        background: #f7f8fa;
        border-top: 1px solid #edf0f4;
    }

    .modal-btn-confirm {
        background: #d40a2f;
        color: #fff;
        border: 0;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
    }

    .modal-btn-cancel {
        background: #cbd5e0;
        color: #2d3748;
        border: 0;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
    }
</style>

<main class="page-shell afspraken-page">
    @if (session('succesmelding'))
        <p class="afspraken-flash-success" data-auto-dismiss role="status">{{ session('succesmelding') }}</p>
    @endif

    @if (session('foutmelding'))
        <p class="afspraken-flash-error" data-auto-dismiss role="status">{{ session('foutmelding') }}</p>
    @endif

    <nav class="afspraken-breadcrumb" aria-label="Kruimelpad">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <span>Mijn afspraken</span>
    </nav>

    <div class="afspraken-heading">
        <h1>Mijn afspraken</h1>
        <a href="{{ route('afspraken.create') }}" class="afspraken-btn-create">Nieuwe afspraak inplannen</a>
    </div>

    <!-- Active/Planned appointments -->
    <section class="afspraken-section" aria-label="Geplande afspraken">
        <h2>Geplande afspraken</h2>

        @if ($geplandeAfspraken->isEmpty())
            <div class="afspraken-empty">Je hebt nog geen afspraken.</div>
        @else
            <div class="afspraken-table-wrap">
                <table class="afspraken-table">
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Tijd</th>
                            <th>Behandeling</th>
                            <th>Medewerker</th>
                            <th>Status</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($geplandeAfspraken as $afspraak)
                            <tr>
                                <td>{{ Carbon\Carbon::parse($afspraak->Datum)->format('d-m-Y') }}</td>
                                <td>{{ Carbon\Carbon::parse($afspraak->Starttijd)->format('H:i') }}</td>
                                <td>{{ $afspraak->medewerkerPerBehandeling->behandeling->Naam }}</td>
                                <td>{{ $afspraak->medewerkerPerBehandeling->medewerker->Voornaam }} {{ $afspraak->medewerkerPerBehandeling->medewerker->Achternaam }}</td>
                                <td>
                                    <span class="afspraken-badge afspraken-badge--{{ strtolower($afspraak->Afspraakstatus) }}">
                                        {{ $afspraak->Afspraakstatus }}
                                    </span>
                                </td>
                                <td>
                                    <!-- Bug US1 Fix: Highly visible Wijzigen button -->
                                    <a href="{{ route('afspraken.edit', $afspraak->Id) }}" class="afspraken-btn-edit">Wijzigen</a>
                                    
                                    <!-- Bug US3 Fix: triggers custom Modal overlay confirmation box -->
                                    <button type="button" class="afspraken-btn-cancel" onclick="openCancelModal({{ $afspraak->Id }})">Annuleren</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <!-- Historie / Eerdere afspraken -->
    <section class="afspraken-section" aria-label="Afsprakenhistorie">
        <h2>Eerdere en geannuleerde afspraken</h2>

        @if ($historischeAfspraken->isEmpty())
            <div class="afspraken-empty">Er zijn geen eerdere afspraken.</div>
        @else
            <div class="afspraken-table-wrap">
                <table class="afspraken-table">
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Tijd</th>
                            <th>Behandeling</th>
                            <th>Medewerker</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historischeAfspraken as $afspraak)
                            <tr>
                                <td>{{ Carbon\Carbon::parse($afspraak->Datum)->format('d-m-Y') }}</td>
                                <td>{{ Carbon\Carbon::parse($afspraak->Starttijd)->format('H:i') }}</td>
                                <td>{{ $afspraak->medewerkerPerBehandeling->behandeling->Naam }}</td>
                                <td>{{ $afspraak->medewerkerPerBehandeling->medewerker->Voornaam }} {{ $afspraak->medewerkerPerBehandeling->medewerker->Achternaam }}</td>
                                <td>
                                    <span class="afspraken-badge afspraken-badge--{{ strtolower($afspraak->Afspraakstatus) }}">
                                        {{ $afspraak->Afspraakstatus }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</main>

<!-- Custom confirmation modal overlay -->
<div class="modal-overlay" id="cancelModalOverlay">
    <div class="modal-card">
        <div class="modal-header">Afspraak annuleren</div>
        <div class="modal-body">
            Weet u zeker dat u deze afspraak wilt annuleren? Deze actie kan niet ongedaan gemaakt worden.
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn-cancel" onclick="closeCancelModal()">Nee, behouden</button>
            <form id="cancelForm" method="POST" action="">
                @csrf
                <button type="submit" class="modal-btn-confirm">Ja, annuleren</button>
            </form>
        </div>
    </div>
</div>

<script>
    window.setTimeout(() => {
        document.querySelectorAll('[data-auto-dismiss]').forEach((melding) => melding.remove());
    }, 3000);

    let activeAfspraakId = null;

    function openCancelModal(afspraakId) {
        activeAfspraakId = afspraakId;
        const form = document.getElementById('cancelForm');
        form.action = `/afspraken/${afspraakId}/annuleren`;
        
        const overlay = document.getElementById('cancelModalOverlay');
        overlay.style.display = 'flex';
    }

    function closeCancelModal() {
        const overlay = document.getElementById('cancelModalOverlay');
        overlay.style.display = 'none';
        activeAfspraakId = null;
    }
</script>

@include('components.site-footer')
