@extends('layouts.app')

@section('title', 'Atur Minat Anak | SKILLPATH')

@section('content')
<section class="onboarding-section">
    <div class="container narrow">
        <div class="section-heading">
            <span class="eyebrow">Personalisasi</span>
            <h1>Kenali anak terlebih dahulu.</h1>
            <p>Pilih minat bersama anak. Suara anak akan dipakai untuk menyempurnakan rekomendasi jalur belajar.</p>
        </div>

        <form method="POST" action="{{ route('onboarding.store') }}" class="form-card onboarding-card">
            @csrf

            <div class="two-fields">
                <label>
                    <span>Nama panggilan anak</span>
                    <input type="text" name="child_name" value="{{ old('child_name', $child?->name) }}" maxlength="60" required>
                    @error('child_name') <small class="field-error">{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Usia</span>
                    <input type="number" name="age" min="5" max="14" value="{{ old('age', $child?->age) }}" required>
                    @error('age') <small class="field-error">{{ $message }}</small> @enderror
                </label>
            </div>

            <fieldset>
                <legend>Minat yang dipilih bersama anak</legend>
                <p class="small-note">Gunakan Cooperative Inquiry sederhana: ajak anak melihat pilihan di bawah dan biarkan ia ikut menentukan 1–5 aktivitas yang membuatnya penasaran atau bersemangat.</p>
                @error('interests') <small class="field-error">{{ $message }}</small> @enderror

                <div class="interest-grid">
                    @foreach ($interests as $interest)
                        @php
                            $selected = in_array(
                                $interest->id,
                                old('interests', $child?->interests?->pluck('id')->all() ?? [])
                            );
                        @endphp

                        <label class="interest-option">
                            <input type="checkbox" name="interests[]" value="{{ $interest->id }}" @checked($selected)>
                            <span class="interest-box">
                                <span class="interest-icon">{{ $interest->icon }}</span>
                                <strong>{{ $interest->name }}</strong>
                                <small>{{ $interest->description }}</small>
                            </span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div class="two-fields">
                <label>
                    <span>Minat utama pilihan anak</span>
                    <select name="favorite_interest_id" required>
                        <option value="">Pilih satu minat utama</option>
                        @foreach($interests as $interest)
                            <option value="{{ $interest->id }}" @selected((string) old('favorite_interest_id', $child?->favorite_interest_id) === (string) $interest->id)>{{ $interest->name }}</option>
                        @endforeach
                    </select>
                    @error('favorite_interest_id') <small class="field-error">{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Kebutuhan belajar utama</span>
                    <select name="learning_need" required>
                        <option value="">Pilih kebutuhan utama</option>
                        @foreach($learningNeeds as $value => $label)
                            <option value="{{ $value }}" @selected(old('learning_need', $child?->learning_need) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('learning_need') <small class="field-error">{{ $message }}</small> @enderror
                </label>
            </div>

            <label>
                <span>Suara anak</span>
                <textarea name="child_voice" rows="3" maxlength="500" required placeholder="Contoh: Aku ingin lebih berani bicara di depan teman dan suka aktivitas yang memakai gambar.">{{ old('child_voice', $child?->child_voice) }}</textarea>
                <small class="small-note">Tuliskan dengan kata-kata anak tentang apa yang ingin ia pelajari atau coba.</small>
                @error('child_voice') <small class="field-error">{{ $message }}</small> @enderror
            </label>

            <div class="payment-form">
                <label>
                    <input type="checkbox" name="co_design_confirmed" value="1" required @checked(old('co_design_confirmed', $child?->co_design_completed_at ? 1 : 0))>
                    <span>Saya sudah mendiskusikan pilihan minat dan kebutuhan ini bersama anak.</span>
                </label>
            </div>
            @error('co_design_confirmed') <small class="field-error">{{ $message }}</small> @enderror

            <button class="btn btn-dark btn-full" type="submit">Buat Rekomendasi Saya</button>
        </form>
    </div>
</section>
@endsection
