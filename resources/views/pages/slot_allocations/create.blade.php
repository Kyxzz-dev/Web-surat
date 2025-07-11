@extends('layout.main')

@section('content')
<div class="container">
    <h4 class="mb-4">Tambah Slot Surat Harian</h4>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('reference.slot-allocations.store') }}" method="POST">
                @csrf

                <input type="date" name="date" class="form-control" required>

                <div class="mb-3">
                    <label for="start_number" class="form-label">Nomor Awal</label>
                    <input type="number" name="start_number" id="start_number"
                           class="form-control" value="{{ old('start_number') }}" required>
                </div>

                <div class="mb-3">
                    <label for="end_number" class="form-label">Nomor Akhir</label>
                    <input type="number" name="end_number" id="end_number"
                           class="form-control" value="{{ old('end_number') }}" required>
                </div>

                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="{{ route('reference.slot-allocations.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
