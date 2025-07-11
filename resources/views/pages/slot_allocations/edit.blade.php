@extends('layout.main')

@section('content')
<div class="container">
    <h4 class="mb-4">Edit Slot Surat Harian</h4>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('reference.slot-allocations.update', $slot->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="date" class="form-label">Tanggal</label>
                    <input type="date" name="date" id="date" class="form-control"
                           value="{{ old('date', $slot->date) }}" required>
                </div>

                <div class="mb-3">
                    <label for="start_number" class="form-label">Nomor Awal</label>
                    <input type="number" name="start_number" id="start_number" class="form-control"
                           value="{{ old('start_number', $slot->start_number) }}" required>
                </div>

                <div class="mb-3">
                    <label for="end_number" class="form-label">Nomor Akhir</label>
                    <input type="number" name="end_number" id="end_number" class="form-control"
                           value="{{ old('end_number', $slot->end_number) }}" required>
                </div>

                <button type="submit" class="btn btn-primary">Perbarui</button>
                <a href="{{ route('reference.slot-allocations.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
