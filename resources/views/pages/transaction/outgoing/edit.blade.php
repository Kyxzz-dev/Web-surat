@extends('layout.main')

@section('content')
<x-breadcrumb :values="[__('menu.transaction.menu'), __('menu.transaction.outgoing_letter'), __('menu.general.edit')]">
</x-breadcrumb>

<div class="card mb-4">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form action="{{ route('transaction.outgoing.update', $data) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body row">
            <input type="hidden" name="id" value="{{ $data->id }}">
            <input type="hidden" name="type" value="{{ $data->type }}">
            <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                <x-input-form :value="$data->reference_number" name="reference_number"
                    :label="__('model.letter.reference_number')" readonly />
            </div>
            <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                <x-input-form :value="$data->to" name="to" :label="__('model.letter.to')" />
            </div>
            <div class="col-sm-12 col-12 col-md-6 col-lg-4">
                <x-input-form :value="$data->agenda_number" name="agenda_number"
                    :label="__('model.letter.agenda_number')" readonly />
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4">
                <label for="classification" class="form-label">Klasifikasi</label>
                <input type="text" class="form-control" id="classification"
                    value="{{ $data->classification->code ?? '-' }}" readonly>
            </div>

            {{-- SUB-KLASIFIKASI --}}
            <div class="col-sm-12 col-md-6 col-lg-4">
                <label for="sub_classification_id" class="form-label">Sub-Klasifikasi</label>
                <input type="text" class="form-control" id="sub_classification_id"
                    value="{{ $data->subClassification->code ?? '-' }}" readonly>
            </div>
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

            {{-- TANGGAL DITERIMA --}}
            <div class="col-sm-12 col-md-6 col-lg-4">
                <x-input-form :value="date('Y-m-d', strtotime($data->received_date))" name="received_date"
                    :label="__('model.letter.received_date')" type="date" readonly />
            </div>

            {{-- TANGGAL SURAT --}}
            <div class="col-sm-12 col-md-6 col-lg-4">
                <x-input-form :value="date('Y-m-d', strtotime($data->letter_date))" name="letter_date"
                    :label="__('model.letter.letter_date')" type="date" readonly />
            </div>


            <div class="col-sm-12 col-12 col-md-12 col-lg-12">
                <x-input-textarea-form :value="$data->description" name="description"
                    :label="__('model.letter.description')" />
            </div>

            <div class="col-sm-12 col-12 col-md-12 col-lg-12">
                <x-input-textarea-form :value="$data->note ?? ''" name="note" :label="__('model.letter.note')" />
            </div>

            <div class="card-footer d-flex gap-2 justify-content-start">
        <button class="btn btn-warning" type="submit">{{ __('menu.general.update') }}</button>
        <a href="{{ route('transaction.outgoing.index') }}" class="btn btn-secondary">{{ __('menu.general.cancel') }}</a>
    </div>

    </form>
</div>
@endsection