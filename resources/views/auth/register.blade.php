@include('components.site-navbar')

<main class="page-shell">
    <section style="
        max-width: 460px;
        padding: 32px;
        border: 1px solid #dce1e8;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 2px 6px rgb(20 25 35 / 0.12);
    ">
        <h1 style="margin: 0 0 20px; color: #6d747d; font-size: 28px;">Account aanmaken</h1>

        @if ($errors->any())
            <div style="margin-bottom: 16px; color: #b00020; font-size: 14px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" style="display: grid; gap: 14px;">
            @csrf

            <label style="display: grid; gap: 6px; color: #69717c; font-weight: 700;">
                Naam
                <input name="name" type="text" value="{{ old('name') }}" required autofocus style="
                    min-height: 38px;
                    padding: 8px 10px;
                    border: 1px solid #cfd6df;
                    border-radius: 7px;
                    font: inherit;
                ">
            </label>

            <label style="display: grid; gap: 6px; color: #69717c; font-weight: 700;">
                E-mailadres
                <input name="email" type="email" value="{{ old('email') }}" required style="
                    min-height: 38px;
                    padding: 8px 10px;
                    border: 1px solid #cfd6df;
                    border-radius: 7px;
                    font: inherit;
                ">
            </label>

            <label style="display: grid; gap: 6px; color: #69717c; font-weight: 700;">
                Wachtwoord
                <input name="password" type="password" required style="
                    min-height: 38px;
                    padding: 8px 10px;
                    border: 1px solid #cfd6df;
                    border-radius: 7px;
                    font: inherit;
                ">
            </label>

            <label style="display: grid; gap: 6px; color: #69717c; font-weight: 700;">
                Herhaal wachtwoord
                <input name="password_confirmation" type="password" required style="
                    min-height: 38px;
                    padding: 8px 10px;
                    border: 1px solid #cfd6df;
                    border-radius: 7px;
                    font: inherit;
                ">
            </label>

            <button type="submit" style="
                min-height: 38px;
                border: 0;
                border-radius: 7px;
                background: #d40a2f;
                color: #fff;
                font: inherit;
                font-weight: 700;
                cursor: pointer;
            ">Account aanmaken</button>
        </form>

        <p style="margin: 18px 0 0; color: #737982; font-size: 14px;">
            Heb je al een account?
            <a href="{{ route('login') }}" style="color: #1c78b2; font-weight: 700;">Inloggen</a>
        </p>
    </section>
</main>

@include('components.site-footer')
