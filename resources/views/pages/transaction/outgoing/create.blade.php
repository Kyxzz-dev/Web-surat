@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.transaction.menu'), __('menu.transaction.outgoing_letter'), __('menu.general.create')]">
    </x-breadcrumb>

    <div class="card mb-4">
        <form action="{{ route('transaction.outgoing.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body row">
                <input type="hidden" name="type" value="outgoing">
                
                <div class="col-md-6 mb-3">
                    <x-input-form name="reference_number" id="reference_number" :label="__('model.letter.reference_number')" value="WIM.2" readonly />
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Nomor Agenda</label>
                    <input type="text" class="form-control" id="remaining_limit" value="-" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="classification_id" class="form-label">Klasifikasi</label>
                    <select class="form-select" id="classification_id" name="classification_id">
                        <option value="">-- Pilih Klasifikasi --</option>
                        @foreach($classifications as $classification)
                            <option value="{{ $classification->id }}">{{ $classification->type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="sub_classification_id" class="form-label">Sub-Klasifikasi</label>
                    <select class="form-select" id="sub_classification_id" name="sub_classification_id">
                        <option value="">-- Pilih Sub-Klasifikasi --</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <x-input-form name="letter_date" id="letter_date" :label="__('model.letter.letter_date')" type="date" />
                </div>

                <div class="col-md-6 mb-3">
                    <x-input-form name="received_date" :label="__('model.letter.received_date')" type="date" />
                </div>

                <div class="col-md-12 mb-3">
                    <x-input-textarea-form name="description" :label="__('model.letter.description')" />
                </div>

                <div class="col-sm-12 col-md-12">
                    <x-input-textarea-form name="note" :label="__('model.letter.note')" />
                </div>

                <div class="col-md-6 mb-3">
                    <x-input-form name="to" :label="__('model.letter.to')" />
                </div>

                <div class="col-md-6 mb-3">
                    <label for="attachments" class="form-label">{{ __('model.letter.attachment') }}</label>
                    <input type="file" class="form-control @error('attachments') is-invalid @enderror" id="attachments" name="attachments[]" multiple />
                    <span class="error invalid-feedback">{{ $errors->first('attachments') }}</span>
                </div>
            </div>

            <div class="card-footer pt-0">
                <button class="btn btn-primary" type="submit">{{ __('menu.general.save') }}</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<!-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('letter_date');
        const referenceField = document.getElementById('reference_number');
        const limitField.value = data.remaining !== null ? `${data.remaining} dari ${data.limit}` : '-';


        function fetchReferenceAndLimit(date) {
            if (!date) return;

            fetch(`/transaction/outgoing/preview-reference-number?letter_date=${date}`)
                .then(res => res.json())
                .then(data => {
                    referenceField.value = data.reference_number || '';
                    limitField.value = data.remaining !== null ? `${data.remaining} nomor tersisa` : '-';
                })
                .catch(() => {
                    referenceField.value = '';
                    limitField.value = '-';
                });
        }

        // Fetch ulang saat user mengganti tanggal
        if (dateInput) {
            dateInput.addEventListener('change', function () {
                fetchReferenceAndLimit(this.value);
            });

            // Inisialisasi jika sudah ada tanggal
            if (dateInput.value) {
                fetchReferenceAndLimit(dateInput.value);
            }
        }
    });
</script> -->
@endpush
