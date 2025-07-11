@extends('layout.main')

@section('content')
    {{-- Breadcrumb dan tombol tambah surat --}}
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif
    <x-breadcrumb
        :values="[__('menu.transaction.menu'), __('menu.transaction.outgoing_letter')]">
        <a href="{{ route('transaction.outgoing.create') }}" class="btn btn-primary">
            {{ __('menu.general.create') }}
        </a>
    </x-breadcrumb>
    {{-- Info Slot Surat Hari Ini --}}
    @php
    $today = now()->toDateString();
    $slot = \App\Models\SlotAllocation::where('date', $today)->first();
@endphp

 @if($slot)
    @php
        $usedCount = \App\Models\Letter::whereDate('letter_date', $slot->date)
            ->where('type', 'outgoing') // ✅ hanya hitung surat keluar
            ->count();

        $totalSlot = $slot->end_number - $slot->start_number + 1;
        $remaining = $totalSlot - $usedCount;
    @endphp

    <div class="alert alert-info mb-3 d-flex justify-content-between align-items-center w-100">
        <div>
            Slot hari ini (<strong>{{ \Carbon\Carbon::parse($slot->date)->translatedFormat('d F Y') }}</strong>):
            <strong>{{ str_pad($slot->start_number, 3, '0', STR_PAD_LEFT) }} - {{ str_pad($slot->end_number, 3, '0', STR_PAD_LEFT) }}</strong><br>
            Tersisa <strong>{{ $remaining }}</strong> dari <strong>{{ $totalSlot }}</strong> slot surat
        </div>

        @if (auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('reference.slot-allocations.edit', $slot->id) }}" class="btn btn-sm btn-warning">
                Edit Slot
            </a>
        @endif
    </div>
@else
    <div class="alert alert-warning mb-3">
        Slot nomor surat hari ini belum diatur.
    </div>
@endif

    {{-- Daftar Surat Keluar --}}
    @foreach($data as $letter)
        <x-letter-card :letter="$letter" />
    @endforeach

    {!! $data->appends(['search' => $search])->links() !!}
@endsection
