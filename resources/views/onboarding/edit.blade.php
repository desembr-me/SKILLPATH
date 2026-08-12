@extends('layouts.app')

@section('title', 'Atur Minat Anak | SKILLPATH')

@section('content')
<section class="onboarding-section">
    <div class="container narrow">
        <div class="section-heading">
            <span class="eyebrow">Personalisasi</span>
            <h1>Kenali anak terlebih dahulu.</h1>
            <p>Pilih minimal satu minat. Pilihan ini dapat diubah kapan saja.</p>
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
                <legend>Minat yang paling disukai</legend>
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

            <button class="btn btn-dark btn-full" type="submit">Buat Rekomendasi Saya</button>
        </form>
    </div>
</section>
@endsection
