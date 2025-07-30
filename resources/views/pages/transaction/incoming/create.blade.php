@extends('layout.main')

@section('content')
<x-breadcrumb
    :values="[__('menu.transaction.menu'), __('menu.transaction.incoming_letter'), __('menu.general.create')]" />



<div class="card mb-4 shadow-sm border-0">
    <form action="{{ route('transaction.incoming.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body row g-3">
            <input type="hidden" name="type" value="incoming">

            {{-- Nomor Surat --}}
            <div class="col-sm-12 col-md-6 col-lg-4">
                <x-input-form name="reference_number" :label="__('model.letter.reference_number')" placeholder="" />
                @error('reference_number')
                <div class="text-danger mt-1 small">{{ $message }}</div>
                @enderror
            </div>

            {{-- Kode Surat --}}
            <div class="col-sm-12 col-md-6 col-lg-4">
                <x-input-form name="letter_code" :label="__('model.letter.letter_code')" :value="$letter_code"
                    placeholder="" readonly />
            </div>

            {{-- Tanggal Surat --}}
            <div class="col-sm-12 col-md-6 col-lg-4">
                <x-input-form name="letter_date" :label="__('model.letter.letter_date')" type="date" />
            </div>

            {{-- Sifat Surat --}}
            <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="mb-3">
                    <label for="letter_nature" class="form-label">Sifat Surat</label>
                    <select class="form-select" id="letter_nature" name="letter_nature" required>
                        <option value="" disabled selected>Pilih sifat surat</option>
                        <option value="Segera">Segera</option>
                        <option value="Sangat Segera">Sangat Segera</option>
                        <option value="Biasa">Biasa</option>
                        <option value="Rahasia">Rahasia</option>
                        <option value="Sangat Rahasia">Sangat Rahasia</option>
                    </select>
                </div>
            </div>

            {{-- Asal Surat (Pengirim) --}}
            <div class="col-sm-12 col-md-6 col-lg-4">
                <x-input-form name="from" :label="__('model.letter.from')" placeholder="Asal Surat" />
            </div>

            {{-- Lampiran --}}
            <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="mb-3">
                    <label for="attachments" class="form-label">{{ __('model.letter.attachment') }}</label>
                    <input type="file" class="form-control @error('attachments') is-invalid @enderror" id="attachments"
                        name="attachments[]" multiple accept="application/pdf">
                    @error('attachments')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted mt-1">
                        * Hanya file PDF yang diizinkan. Ukuran maksimal per file 10MB.
                    </small>
                </div>
            </div>

            {{-- Ringkasan / Perihal --}}
            <div class="col-sm-12">
                <x-input-textarea-form name="description" :label="__('model.letter.description')"
                    placeholder="Perihal" />
            </div>

            {{-- Keterangan --}}
            <div class="col-sm-12">
                <x-input-textarea-form name="note" :label="__('model.letter.note')" />
            </div>
        </div>

        <div class="card-footer pt-0">
            <button class="btn btn-primary" type="submit">{{ __('menu.general.save') }}</button>
        </div>
    </form>
</div>
@endsection