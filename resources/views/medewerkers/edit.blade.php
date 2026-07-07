@include('components.site-navbar')

{{-- 
    Medewerkers Edit View (Wijzigingspagina)
    Toont het bewerkingsformulier van een medewerker.
    Voldoet aan MVC (View), PSR-12, responsiviteit, validatie (client & server) en klantterugkoppeling.
--}}

<style>
    .required-marker {
        color: #d40a2f;
    }
</style>

<main class="page-shell">
    {{-- Foutmelding aan de eindgebruiker (Terugkoppeling acties) --}}
    @if (session('error'))
        <div style="margin-bottom: 24px; padding: 16px; border: 1px solid #f5c6cb; border-radius: 8px; background-color: #fce4e4; color: #c00000; font-size: 15px; font-weight: 500;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Broodkruimelpad --}}
    <div style="margin-bottom: 24px; font-size: 14px; color: #8a8f96;">
        <a href="{{ route('home') }}" style="color: #d40a2f; text-decoration: none;">Home</a> /
        <a href="{{ route('medewerkers.index') }}" style="color: #d40a2f; text-decoration: none;">Medewerkers</a> / Wijzigen
    </div>

    {{-- Formulier Titel --}}
    <h1 style="color: #d40a2f; font-size: 28px; font-weight: 700; margin-bottom: 24px;">
        Medewerker wijzigen <span style="color: #8a8f96; font-weight: 400;">{{ $medewerker->naam }}</span>
    </h1>

    {{-- Bewerkingskaart --}}
    <div style="border: 1px solid #dce1e8; border-radius: 12px; padding: 32px; background: #fff; max-width: 800px; box-shadow: 0 2px 6px rgb(20 25 35 / 0.12); margin-bottom: 32px; box-sizing: border-box;">
        
        <form method="POST" action="{{ route('medewerkers.update', $medewerker->Id) }}">
            @csrf
            @method('PUT')

            {{-- 
                Responsieve Grid Lay-out (Eis 10)
                Maakt gebruik van repeat(auto-fit, minmax(300px, 1fr)) zodat de velden op mobiel
                automatisch onder elkaar schuiven en op grotere schermen in twee kolommen staan.
            --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 24px;">
                
                {{-- Linker Kolom --}}
                <div style="display: grid; gap: 16px; align-content: start;">
                    
                    {{-- Naam (Client-side required validatie) --}}
                    <label style="display: grid; gap: 6px; color: #20242a; font-weight: 700; font-size: 14px; width: 100%;">
                        <span>Naam <span class="required-marker">*</span></span>
                        <input name="naam" type="text" value="{{ old('naam', $medewerker->naam) }}" required style="min-height: 38px; padding: 8px 10px; border: 1px solid {{ $errors->has('naam') ? '#b00020' : '#cfd6df' }}; border-radius: 7px; font: inherit; width: 100%; box-sizing: border-box;">
                        @error('naam')
                            <span style="color: #b00020; font-size: 12px; font-weight: 400; margin-top: 4px;">{{ $message }}</span>
                        @enderror
                    </label>

                    {{-- Geboortedatum (Client-side required validatie) --}}
                    <label style="display: grid; gap: 6px; color: #20242a; font-weight: 700; font-size: 14px; width: 100%;">
                        <span>Geboortedatum <span class="required-marker">*</span></span>
                        <div style="position: relative; display: grid; width: 100%;">
                            <input name="geboortedatum" type="text" placeholder="dd-mm-yyyy" value="{{ old('geboortedatum', $medewerker->Geboortedatum ? $medewerker->Geboortedatum->format('d-m-Y') : '') }}" required style="min-height: 38px; padding: 8px 36px 8px 10px; border: 1px solid {{ $errors->has('geboortedatum') ? '#b00020' : '#cfd6df' }}; border-radius: 7px; font: inherit; width: 100%; box-sizing: border-box;">
                            {{-- Agenda Icoon --}}
                            <svg style="position: absolute; right: 12px; top: 11px; width: 16px; height: 16px; color: #69717c; pointer-events: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                            </svg>
                        </div>
                        @error('geboortedatum')
                            <span style="color: #b00020; font-size: 12px; font-weight: 400; margin-top: 4px;">{{ $message }}</span>
                        @enderror
                    </label>

                    {{-- Account e-mail (Read-only/Niet aanpasbaar conform de wireframes en security) --}}
                    <label style="display: grid; gap: 6px; color: #69717c; font-weight: 700; font-size: 14px; width: 100%;">
                        Account e-mail
                        <input type="text" value="{{ $medewerker->user->email ?? '' }}" readonly style="min-height: 38px; padding: 8px 10px; border: 1px solid #cfd6df; border-radius: 7px; font: inherit; background-color: #f5f6fa; color: #7d8590; cursor: not-allowed; width: 100%; box-sizing: border-box;">
                    </label>

                    {{-- Huisnummer & Toevoeging --}}
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px; width: 100%;">
                        <label style="display: grid; gap: 6px; color: #20242a; font-weight: 700; font-size: 14px;">
                            <span>Huisnummer <span class="required-marker">*</span></span>
                            <input name="huisnummer" type="text" value="{{ old('huisnummer', $medewerker->contact->Huisnummer ?? '') }}" required style="min-height: 38px; padding: 8px 10px; border: 1px solid {{ $errors->has('huisnummer') ? '#b00020' : '#cfd6df' }}; border-radius: 7px; font: inherit; width: 100%; box-sizing: border-box;">
                            @error('huisnummer')
                                <span style="color: #b00020; font-size: 12px; font-weight: 400; margin-top: 4px;">{{ $message }}</span>
                            @enderror
                        </label>
                        <label style="display: grid; gap: 6px; color: #20242a; font-weight: 700; font-size: 14px;">
                            Toevoeging
                            <input name="toevoeging" type="text" value="{{ old('toevoeging', $medewerker->contact->Toevoeging ?? '') }}" style="min-height: 38px; padding: 8px 10px; border: 1px solid {{ $errors->has('toevoeging') ? '#b00020' : '#cfd6df' }}; border-radius: 7px; font: inherit; width: 100%; box-sizing: border-box;">
                            @error('toevoeging')
                                <span style="color: #b00020; font-size: 12px; font-weight: 400; margin-top: 4px;">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>

                    {{-- Plaats --}}
                    <label style="display: grid; gap: 6px; color: #20242a; font-weight: 700; font-size: 14px; width: 100%;">
                        <span>Plaats <span class="required-marker">*</span></span>
                        <input name="plaats" type="text" value="{{ old('plaats', $medewerker->contact->Plaats ?? '') }}" required style="min-height: 38px; padding: 8px 10px; border: 1px solid {{ $errors->has('plaats') ? '#b00020' : '#cfd6df' }}; border-radius: 7px; font: inherit; width: 100%; box-sizing: border-box;">
                        @error('plaats')
                            <span style="color: #b00020; font-size: 12px; font-weight: 400; margin-top: 4px;">{{ $message }}</span>
                        @enderror
                    </label>

                </div>

                {{-- Rechter Kolom --}}
                <div style="display: grid; gap: 16px; align-content: start;">
                    
                    {{-- Specialisatie met selectieveld --}}
                    <label style="display: grid; gap: 6px; color: #20242a; font-weight: 700; font-size: 14px; position: relative; width: 100%;">
                        <span>Specialisatie <span class="required-marker">*</span></span>
                        <div style="position: relative; display: grid; width: 100%;">
                            {{-- Dropdown met custom styling en validatie visualisatie --}}
                            <select name="specialisatie" style="min-height: 38px; padding: 8px {{ $errors->has('specialisatie') ? '36px' : '12px' }} 8px 12px; border: 1px solid {{ $errors->has('specialisatie') ? '#b00020' : '#cfd6df' }}; border-radius: 7px; font: inherit; background: #fff; cursor: pointer; appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%252369717c%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 12px top 50%; background-size: 10px auto; width: 100%; box-sizing: border-box;">
                                @foreach($specialisaties as $spec)
                                    <option value="{{ $spec }}" {{ old('specialisatie', $medewerker->Specialisatie) == $spec ? 'selected' : '' }}>{{ $spec }}</option>
                                @endforeach
                            </select>
                            {{-- Validatiefout icoon (Eis 9 en 12) --}}
                            @if($errors->has('specialisatie'))
                                <svg style="position: absolute; right: 32px; top: 11px; width: 16px; height: 16px; color: #b00020; pointer-events: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                </svg>
                            @endif
                        </div>
                        @error('specialisatie')
                            <span style="color: #b00020; font-size: 12px; font-weight: 400; margin-top: 4px; line-height: 1.45;">{{ $message }}</span>
                        @enderror
                    </label>

                    {{-- Contact e-mail (Client-side required en type="email" validatie) --}}
                    <label style="display: grid; gap: 6px; color: #20242a; font-weight: 700; font-size: 14px; width: 100%;">
                        <span>Contact e-mail <span class="required-marker">*</span></span>
                        <input name="email" type="email" value="{{ old('email', $medewerker->contact->Email ?? '') }}" required style="min-height: 38px; padding: 8px 10px; border: 1px solid {{ $errors->has('email') ? '#b00020' : '#cfd6df' }}; border-radius: 7px; font: inherit; width: 100%; box-sizing: border-box;">
                        @error('email')
                            <span style="color: #b00020; font-size: 12px; font-weight: 400; margin-top: 4px;">{{ $message }}</span>
                        @enderror
                    </label>

                    {{-- Straatnaam --}}
                    <label style="display: grid; gap: 6px; color: #20242a; font-weight: 700; font-size: 14px; width: 100%;">
                        <span>Straatnaam <span class="required-marker">*</span></span>
                        <input name="straatnaam" type="text" value="{{ old('straatnaam', $medewerker->contact->Straatnaam ?? '') }}" required style="min-height: 38px; padding: 8px 10px; border: 1px solid {{ $errors->has('straatnaam') ? '#b00020' : '#cfd6df' }}; border-radius: 7px; font: inherit; width: 100%; box-sizing: border-box;">
                        @error('straatnaam')
                            <span style="color: #b00020; font-size: 12px; font-weight: 400; margin-top: 4px;">{{ $message }}</span>
                        @enderror
                    </label>

                    {{-- Postcode --}}
                    <label style="display: grid; gap: 6px; color: #20242a; font-weight: 700; font-size: 14px; width: 100%;">
                        <span>Postcode <span class="required-marker">*</span></span>
                        <input name="postcode" type="text" value="{{ old('postcode', $medewerker->contact->Postcode ?? '') }}" required style="min-height: 38px; padding: 8px 10px; border: 1px solid {{ $errors->has('postcode') ? '#b00020' : '#cfd6df' }}; border-radius: 7px; font: inherit; width: 100%; box-sizing: border-box;">
                        @error('postcode')
                            <span style="color: #b00020; font-size: 12px; font-weight: 400; margin-top: 4px;">{{ $message }}</span>
                        @enderror
                    </label>

                    {{-- Mobiel --}}
                    <label style="display: grid; gap: 6px; color: #20242a; font-weight: 700; font-size: 14px; width: 100%;">
                        <span>Mobiel <span class="required-marker">*</span></span>
                        <input name="mobiel" type="text" value="{{ old('mobiel', $medewerker->contact->Mobiel ?? '') }}" required style="min-height: 38px; padding: 8px 10px; border: 1px solid {{ $errors->has('mobiel') ? '#b00020' : '#cfd6df' }}; border-radius: 7px; font: inherit; width: 100%; box-sizing: border-box;">
                        @error('mobiel')
                            <span style="color: #b00020; font-size: 12px; font-weight: 400; margin-top: 4px;">{{ $message }}</span>
                        @enderror
                    </label>

                </div>

            </div>

            {{-- Opmerking (Volledige breedte) --}}
            <label style="display: grid; gap: 6px; color: #20242a; font-weight: 700; font-size: 14px; margin-bottom: 24px; width: 100%;">
                Opmerking
                <textarea name="opmerking" rows="3" style="padding: 8px 10px; border: 1px solid {{ $errors->has('opmerking') ? '#b00020' : '#cfd6df' }}; border-radius: 7px; font: inherit; resize: vertical; width: 100%; box-sizing: border-box;">{{ old('opmerking', $medewerker->Opmerking) }}</textarea>
                @error('opmerking')
                    <span style="color: #b00020; font-size: 12px; font-weight: 400; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </label>

            <span style="color: #8a8f96; font-size: 13px;">Velden met een * zijn verplicht.</span>

            {{-- Formulier Actieknoppen --}}
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="submit" style="min-height: 38px; padding: 0 16px; border: 0; border-radius: 7px; background: #d40a2f; color: #fff; font: inherit; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;">Opslaan</button>
                <a href="{{ route('medewerkers.show', $medewerker->Id) }}" style="display: inline-flex; min-height: 38px; align-items: center; justify-content: center; padding: 0 16px; border-radius: 7px; background: #737982; color: #fff; font-weight: 700; text-decoration: none; font-size: 14px;">Terug</a>
            </div>

        </form>

    </div>
</main>

@include('components.site-footer')
