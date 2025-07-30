@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('reference.classification.index') }}/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal input#code').val($(this).data('code'));
            $('#editModal input#type').val($(this).data('type'));
            $('#editModal input#description').val($(this).data('description'));
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb :values="[__('menu.reference.menu'), __('menu.reference.classification')]">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            {{ __('menu.general.create') }}
        </button>
    </x-breadcrumb>

    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('model.classification.code') }}</th>
                        <th>{{ __('model.classification.type') }}</th>
                        <th>{{ __('model.classification.description') }}</th>
                        <th>{{ __('menu.general.action') }}</th>
                    </tr>
                </thead>
                @if($data->count())
                    <tbody>
                    @foreach($data as $classification)
                        <tr>
                            <td>{{ $classification->code }}</td>
                            <td>{{ $classification->type }}</td>
                            <td>{{ $classification->description }}</td>
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $classification->id }}"
                                        data-code="{{ $classification->code }}"
                                        data-type="{{ $classification->type }}"
                                        data-description="{{ $classification->description }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>

                                <form action="{{ route('reference.classification.destroy', $classification) }}" method="post" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                  <button class="btn btn-danger btn-sm btn-delete" type="button">
                                    {{ __('menu.general.delete') }}
                                </button>
                                </form>

                                <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#subModal{{ $classification->id }}">
                                    Tambah Sub
                                </button>
                            </td>
                        </tr>

                        @if($classification->subClassifications->count())
                            <tr>
                                <td colspan="4">
                                    <ul class="ms-4">
                                        @foreach($classification->subClassifications as $sub)
                                            <li>
                                                <strong>{{ $sub->code }}</strong>: {{ $sub->description }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                @else
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center">
                                {{ __('menu.general.empty') }}
                            </td>
                        </tr>
                    </tbody>
                @endif
                <tfoot class="table-border-bottom-0">
                    <tr>
                        <th>{{ __('model.classification.code') }}</th>
                        <th>{{ __('model.classification.type') }}</th>
                        <th>{{ __('model.classification.description') }}</th>
                        <th>{{ __('menu.general.action') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {!! $data->appends(['search' => $search])->links() !!}

    {{-- Modal Tambah Klasifikasi --}}
    <div class="modal fade" id="createModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="{{ route('reference.classification.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('menu.general.create') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <x-input-form name="code" :label="__('model.classification.code')" />
                    <x-input-form name="type" :label="__('model.classification.type')" />
                    <x-input-form name="description" :label="__('model.classification.description')" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('menu.general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('menu.general.save') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Klasifikasi --}}
    <div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('menu.general.edit') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <x-input-form name="code" :label="__('model.classification.code')" />
                    <x-input-form name="type" :label="__('model.classification.type')" />
                    <x-input-form name="description" :label="__('model.classification.description')" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('menu.general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('menu.general.update') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Tambah Sub-Klasifikasi per Klasifikasi --}}
    @foreach($data as $classification)
        <div class="modal fade" id="subModal{{ $classification->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('reference.classification.storeSub') }}">
                    @csrf
                    <input type="hidden" name="classification_id" value="{{ $classification->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Sub-Klasifikasi: {{ $classification->code }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <x-input-form name="code" label="Kode Sub-Klasifikasi" />
                        <x-input-form name="description" label="Deskripsi Sub-Klasifikasi" />
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
@push('script')
<script>
    $(document).on('submit', 'form:has(.btn-delete-confirm)', function (e) {
        const confirmed = confirm('Apakah Anda yakin ingin menghapus klasifikasi ini?');
        if (!confirmed) {
            e.preventDefault();
        }
    });
</script>
@endpush