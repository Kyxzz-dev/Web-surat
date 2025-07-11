@extends('layout.main')
@push('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
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
    :values="[__('menu.transaction.menu'), __('menu.transaction.outgoing_letter'), __('menu.general.create')]">
</x-breadcrumb>

<div class="card mb-4">
    <form action="{{ route('transaction.outgoing.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body row">
            <input type="hidden" name="type" value="outgoing">

            <div class="col-sm-12 col-md-6 col-lg-4">
                <x-input-form name="reference_number" :label="__('model.letter.reference_number')" value="WIM.2"
                    readonly />
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4">
                <x-input-form name="agenda_number" :label="__('Nomor Urut Surat')"  readonly />
                <div id="slot-info" class="mt-1 alert alert-info py-2 px-3 d-none" style="font-size: 14px;"></div>
            </div>

            <div class="col-sm-12 col-md-6 col-lg-4">
                <x-input-form name="to" :label="__('model.letter.to')" />
            </div>

            <div class="col-sm-12 col-md-6 col-lg-4">
                <label for="classification_id" class="form-label">Klasifikasi</label>
                <select class="form-select" id="classification_id" name="classification_id">
                    <option value="">-- Pilih Klasifikasi --</option>
                    @foreach($classifications as $classification)
                    <option value="{{ $classification->id }}" data-code="{{ $classification->code }}">
                        {{ $classification->code }} {{ $classification->type }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-12 col-md-6 col-lg-4">
                <label for="sub_classification_id" class="form-label">Sub-Klasifikasi</label>
                <select class="form-select" id="sub_classification_id" name="sub_classification_id">
                    <option value="">-- Pilih Sub-Klasifikasi --</option>
                </select>
            </div>

            {{-- Sifat Surat --}}
            <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="mb-3">
                        <label for="letter_nature" class="form-label">Sifat Surat</label>
                        <select class="form-select" id="letter_nature" name="letter_nature" required>
                            <option value="" disabled selected>Pilih sifat surat</option>
                            <option value="Penting">Penting</option>
                            <option value="Sangat Penting">Sangat Penting</option>
                            <option value="Rahasia">Rahasia</option>
                            <option value="Sangat Rahasia">Sangat Rahasia</option>
                        </select>
                    </div>
                </div>

            <div class="col-sm-12 col-md-6 col-lg-4">
                <x-input-form name="letter_date" id="letter_date" :label="__('model.letter.letter_date')" type="date" />
            </div>

            <div class="col-sm-12 col-md-6 col-lg-4">
                <x-input-form name="received_date" :label="__('model.letter.received_date')" type="date" />
            </div>

            <div class="col-md-12 mb-3">
                <x-input-textarea-form name="description" :label="__('model.letter.description')" />
            </div>

            <div class="col-sm-12 col-md-12">
                <x-input-textarea-form name="note" :label="__('model.letter.note')" />
            </div>

            

        <div class="card-footer pt-0">
            <button class="btn btn-primary" type="submit">{{ __('menu.general.save') }}</button>
        </div>
    </form>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function() {
    $('#classification_id').select2({
        placeholder: '-- Pilih Klasifikasi --',
        width: '100%'
    });

    $('#sub_classification_id').select2({
        placeholder: '-- Pilih Sub-Klasifikasi --',
        width: '100%'
    });

    $('#classification_id').on('change', function() {
        const classificationId = $(this).val();
        $('#sub_classification_id').empty().append(
            '<option value="">-- Pilih Sub-Klasifikasi --</option>');

        if (classificationId) {
            fetch(`/get-sub-classifications/${classificationId}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(item => {
                        const option = new Option(`${item.code} - ${item.description}`, item
                            .id, false, false);
                        $(option).attr('data-code', item.code);
                        $('#sub_classification_id').append(option);
                    });
                    $('#sub_classification_id').trigger('change');
                })
                .catch(err => console.error('Gagal load sub-klasifikasi:', err));
        }
    });
});

function generateReferenceNumber() {
    const klasifikasiCode = $('#classification_id option:selected').data('code') || '';
    const subCode = $('#sub_classification_id option:selected').data('code') || '';
    const agenda = $('input[name="agenda_number"]').val() || '';

    if (klasifikasiCode && agenda) {
        const paddedAgenda = agenda.padStart(3, '0'); // contoh: 031

        // Ambil 2 huruf pertama dari klasifikasiCode
        const shortKlasifikasiCode = klasifikasiCode.substring(0, 2).toUpperCase();

        let refNum = `WIM.2-${shortKlasifikasiCode}`;
        if (subCode) {
            refNum += `-${subCode}`;
        }
        refNum += `-${paddedAgenda}`;

        $('input[name="reference_number"]').val(refNum);
    } else {
        $('input[name="reference_number"]').val('WIM.2');
    }
}

$('#classification_id, #sub_classification_id').on('change', generateReferenceNumber);
$('input[name="agenda_number"]').on('input', generateReferenceNumber);

function fetchAgendaNumberByDate(date) {
    fetch(`/outgoing/next-agenda-number?letter_date=${date}`)
        .then(res => res.json())
        .then(data => {
            const slotInfo = $('#slot-info');

            if (data.next_number) {
                $('input[name="agenda_number"]').val(data.next_number);
                generateReferenceNumber();

                const totalSlot = data.slot_end - data.slot_start + 1;
                const remaining = totalSlot - data.used;

                slotInfo.removeClass('d-none').html(`
                    Tersisa <strong>${remaining}</strong> dari <strong>${totalSlot}</strong> slot surat hari ini.
                `);
            } else if (data.error) {
                alert(data.error);
                $('input[name="agenda_number"]').val('');
                $('input[name="reference_number"]').val('WIM.2');
                slotInfo.addClass('d-none').html('');
            }
        })
        .catch(err => {
            console.error('Gagal fetch agenda number:', err);
            $('#slot-info').addClass('d-none').html('');
        });
}

// Saat tanggal diubah oleh user
$('#letter_date').on('change', function() {
    const selectedDate = $(this).val();
    if (selectedDate) {
        fetchAgendaNumberByDate(selectedDate);
    }
});

// Load awal saat halaman dibuka
$(document).ready(function() {
    const initialDate = $('#letter_date').val();
    if (initialDate) {
        fetchAgendaNumberByDate(initialDate);
    }
});
</script>

@endpush