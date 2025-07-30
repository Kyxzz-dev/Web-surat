@extends('layout.main')

@section('content')
<div class="container">
    <h4 class="mb-4">Daftar Slot Surat Harian</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tombol Tambah Slot --}}
    <a href="{{ route('reference.slot-allocations.create') }}" class="btn btn-primary mb-3">+ Tambah Slot</a>

    {{-- Tabel Daftar Slot --}}
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th>Tanggal</th>
                        <th>Nomor Awal</th>
                        <th>Nomor Akhir</th>
                        <th style="width: 20%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($slots as $index => $slot)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($slot->date)->translatedFormat('d F Y') }}</td>
                            <td>{{ $slot->start_number }}</td>
                            <td>{{ $slot->end_number }}</td>
                            <td>
                                <a href="{{ route('reference.slot-allocations.edit', $slot->id) }}" class="btn btn-warning btn-sm">Edit</a>

                                <form action="{{ route('reference.slot-allocations.destroy', $slot->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                      <button class="btn btn-danger btn-sm btn-delete" type="button">
                                    {{ __('menu.general.delete') }}
                                </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada slot surat</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
