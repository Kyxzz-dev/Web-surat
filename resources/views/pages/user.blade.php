@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('user.index') }}/' + id);
            $('#editModal input:hidden#id').val(id);
            $('#editModal input#name').val($(this).data('name'));
            $('#editModal input#nip').val($(this).data('nip'));
            $('#editModal input#phone').val($(this).data('phone'));
            $('#editModal input#email').val($(this).data('email'));
            if ($(this).data('active') == 1) {
                $('#editModal input#is_active').attr('checked', 1)
            } else {
                $('#editModal input#is_active').removeAttribute('checked');
            }
        });
    </script>
@endpush

@section('content')
<x-breadcrumb :values="[__('menu.users')]">
    <div class="d-flex gap-2">
        <a href="{{ route('user.print') }}" target="_blank" class="btn btn-warning">Cetak</a>
    </div>
</x-breadcrumb>


    <div class="card mb-5">
    <!-- Header laporan -->
    <div class="card-header text-center" style="background-color: white; border-bottom: none;">
        <h4 class="mb-0" style="color: #f58b00;">Laporan Data Pengguna</h4>
        <small class="text-muted">Per {{ \Carbon\Carbon::now()->format('d-m-Y') }}</small>
    </div>

    <div class="table-responsive text-nowrap">
        <table class="table table-striped" style="background-color: white;">
            <thead>
            <tr style="color: #f58b00;">
                <th style="text-align: center;">No</th>
                <th>{{ __('model.user.name') }}</th>
                <th>{{ __('model.user.email') }}</th>
                <th>{{  __('model.user.nip')}}</th>
                <th>{{  __('model.user.bidang')}}</th>
                <th>{{ __('model.user.phone') }}</th>
                <th>{{ __('model.user.is_active') }}</th>
                <th>{{ __('menu.general.action') }}</th>
            </tr>
            </thead>
            @if($data)
                <tbody>
                @foreach($data as $index => $user)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->nip }}</td>
                        <td>{{ $user->bidang }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>
                            <span class="badge bg-label-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ __('model.user.' . ($user->is_active ? 'active' : 'nonactive')) }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-info btn-sm btn-edit"
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}"
                                    data-nip="{{ $user->nip }}"
                                    data-phone="{{ $user->phone }}"
                                    data-active="{{ $user->is_active }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal">
                                {{ __('menu.general.edit') }}
                            </button>
                            <form action="{{ route('user.destroy', $user) }}" class="d-inline" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-delete" type="button">
                                    {{ __('menu.general.delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            @else
                <tbody>
                <tr>
                    <td colspan="6" class="text-center">
                        {{ __('menu.general.empty') }}
                    </td>
                </tr>
                </tbody>
            @endif
        </table>
    </div>

    <!-- Catatan bawah tabel -->
<div class="mt-3 px-4 pb-3">
    <div class="mb-0" role="alert" style="background-color: #fff4e5; border-left: 4px solid #f58b00; padding: 10px 15px; border-radius: 4px; font-size: 0.9rem; color: #663c00;">
        <strong>Catatan:</strong> <br>
        <span class="ms-2">* Jika ingin menonaktifkan akun, klik tombol <strong>Edit</strong> pada akun yang dimaksud, lalu hilangkan centang pada opsi "<strong>Masih aktif?</strong>".</span>
    </div>
</div>

</div>


    {!! $data->appends(['search' => $search])->links() !!}



    <!-- Create Modal -->
    <div class="modal fade" id="createModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="{{ route('user.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalTitle">{{ __('menu.general.create') }}</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <x-input-form name="name" :label="__('model.user.name')"/>
                    <x-input-form name="nip" :label="__('model.user.nip')"/>
                    <x-input-form name="email" :label="__('model.user.email')" type="email"/>
                    <x-input-form name="phone" :label="__('model.user.phone')"/>
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
    

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalTitle">{{ __('menu.general.edit') }}</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <x-input-form name="name" :label="__('model.user.name')"/>
                    <x-input-form name="nip" :label="__('model.user.nip')"/>
                    <x-input-form name="email" :label="__('model.user.email')" type="email"/>
                    <x-input-form name="phone" :label="__('model.user.phone')"/>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="true" id="is_active">
                        <label class="form-check-label" for="is_active"> {{ __('model.user.is_active') }} </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="reset_password" value="true" id="reset_password">
                        <label class="form-check-label" for="reset_password"> {{ __('model.user.reset_password') }} </label>
                    </div>
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
@endsection