@extends('layout.main')

@section('content')
    <x-breadcrumb :values="[__('menu.transaction.menu'), __('menu.transaction.incoming_letter'), __('menu.general.create')]"/>

    <div class="card mb-4">
        <form action="{{ route('transaction.incoming.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <input type="hidden" name="type" value="incoming">

                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="classification_code" class="form-label">{{ __('model.letter.classification_code') }}</label>
                        <select class="form-select @error('classification_code') is-invalid @enderror" id="classification_code" name="classification_code">
                            <option value="">-- Pilih Kode Klasifikasi --</option>
                            @foreach($classifications as $classification)
                                <option value="{{ $classification->code }}" @selected(old('classification_code') == $classification->code)>
                                    {{ $classification->code }} - {{ $classification->type }}
                                </option>
                            @endforeach
                        </select>
                        @error('classification_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                

                <div class="col-sm-12 col-md-6 col-lg-4">
                    <x-input-form name="reference_number" :label="__('model.letter.reference_number')" :value="$reference_number" readonly />
                </div>

                <div class="col-sm-12 col-md-6 col-lg-6">
                    <x-input-form name="letter_date" :label="__('model.letter.letter_date')" type="date" />
                </div>

                <div class="col-sm-12 col-md-6 col-lg-6">
                    <x-input-form name="received_date" :label="__('model.letter.received_date')" type="date" />
                </div>

                <div class="col-sm-12 col-md-12">
                    <x-input-textarea-form name="description" :label="__('model.letter.description')" />
                </div>

                <div class="col-sm-12 col-md-6 col-lg-4">
                    <x-input-form name="agenda_number" :label="__('model.letter.agenda_number')" />
                </div>

                <div class="col-sm-12 col-md-6 col-lg-4">
                    <x-input-form name="from" :label="__('model.letter.from')" />
                </div>

                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="attachments" class="form-label">{{ __('model.letter.attachment') }}</label>
                        <input type="file" class="form-control @error('attachments') is-invalid @enderror" id="attachments" name="attachments[]" multiple />
                        @error('attachments')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card-footer pt-0">
                <button class="btn btn-primary" type="submit">{{ __('menu.general.save') }}</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    function updateReferenceNumber() {
        let code = document.getElementById('classification_code').value;
        let date = document.querySelector('input[name="letter_date"]').value;

        if (!code || !date) return;

        fetch({{ route('transaction.incoming.previewReferenceNumber') }}?classification_code=${code}&letter_date=${date})
            .then(res => res.json())
            .then(data => {
                document.querySelector('input[name="reference_number"]').value = data.reference_number;
            });
    }

    document.getElementById('classification_code').addEventListener('change', updateReferenceNumber);
    document.querySelector('input[name="letter_date"]').addEventListener('change', updateReferenceNumber);
</script>
@endpush