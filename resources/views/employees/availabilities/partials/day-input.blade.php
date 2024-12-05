{{-- resources/views/partials/day-input.blade.php --}}
<div class="col-md-6 col-lg-4">
    <div class="card h-100">
        <div class="card-header">
            <h5 class="mb-0">{{ $dayName }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label">Startzeit</label>
                    <select class="form-select" name="{{ $dayKey }}_start">
                        <option value="">Bitte wählen</option>
                        
                        @foreach ($timeOptions as $time)
                            <option value="{{ $time }}" 
                                {{ isset($availabilities[$dayKey]['start_time']) && $availabilities[$dayKey]['start_time'] === $time ? 'selected' : '' }}>
                                {{ $time }} Uhr
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">Endzeit</label>
                    <select class="form-select" name="{{ $dayKey }}_end">
                        <option value="">Bitte wählen</option>
                        @foreach ($timeOptions as $time)
                            <option value="{{ $time }}"
                                {{ isset($availabilities[$dayKey]['end_time']) && $availabilities[$dayKey]['end_time'] === $time ? 'selected' : '' }}>
                                {{ $time }} Uhr
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>