@extends('layout.main')

@push('script')
    <script>
        $('input#accountActivation').on('change', function () {
           $('button.deactivate-account').attr('disabled', !$(this).is(':checked'));
        });

        document.addEventListener('DOMContentLoaded', function (e) {
            (function () {
                // Update/reset user image of account page
                let accountUserImage = document.getElementById('uploadedAvatar');
                const fileInput = document.querySelector('.account-file-input'),
                    resetFileInput = document.querySelector('.account-image-reset');

                if (accountUserImage) {
                    const resetImage = accountUserImage.src;
                    fileInput.onchange = () => {
                        if (fileInput.files[0]) {
                            accountUserImage.src = window.URL.createObjectURL(fileInput.files[0]);
                        }
                    };
                    resetFileInput.onclick = () => {
                        fileInput.value = '';
                        accountUserImage.src = resetImage;
                    };
                }
            })();
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb
        :values="[__('navbar.profile.profile')]">
    </x-breadcrumb>

    <div class="row">
        <div class="col">
            {{-- Tab --}}
            @if(auth()->user()->role == 'admin')
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item">
                    <a class="nav-link active" href="javascript:void(0);">{{ __('navbar.profile.profile') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('settings.show') }}">{{ __('navbar.profile.settings') }}</a>
                </li>
            </ul>
            @endif

            <div class="card mb-4">
                <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!-- Account -->
                    <div class="card-body">
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img src="{{ $data->profile_picture }}" alt="user-avatar"
                                 class="d-block rounded-circle object-fit-cover" height="100" width="100" id="uploadedAvatar">
                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                    <span class="d-none d-sm-block">{{ __('menu.general.upload') }}</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                    <input type="file" name="profile_picture" id="upload" class="account-file-input" hidden=""
                                           accept="image/png, image/jpeg">
                                </label>
                                <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                    <i class="bx bx-reset d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">{{ __('menu.general.cancel') }}</span>
                                </button>

                                <p class="text-muted mb-0">* Maksimal file 800 kb (JPG, PNG)</p>
                            </div>
                        </div>
                    </div>
                    <hr class="my-0">
                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" name="id" value="{{ $data->id }}">
                            <div class="col-md-6 col-lg-12">
                                <x-input-form name="name" :label="__('model.user.name')" :value="$data->name" />
                            </div>
                            <div class="col-md-6">
                                <x-input-form name="email" :label="__('model.user.email')" :value="$data->email" />
                            </div>
                            @if(auth()->user()->role == 'staff')
                                <div class="col-md-6">
                                    <x-input-form name="nip" :label="'nip'" :value="$data->nip" />
                                </div>
                            @endif
                            <div class="col-md-6">
                                <x-input-form name="phone" :label="__('model.user.phone')" :value="$data->phone ?? ''" />
                            </div>
                            <!-- <div class="col-sm-12 col-md-6 col-lg-4">
                                <div class="mb-3":>
                                    <label for="bidang" class="form-label">Bidang</label>
                                    <select name="bidang" id="bidang" class="form-select" required>
                                        <option value="">-- Pilih Bidang --</option>
                                        <option value="umum">Tata Usaha & Umum</option>
                                        <option value="pengawasan">Pengawasan & Penindakan Keimigrasian</option>
                                        <option value="intelijen">Intelijen & Kepatuhan Internal</option>
                                        <option value="perjalanan">Dokumen Perjalanan, Izin Tinggal & Status Keimigrasian</option>
                                    </select>
                                </div>
                            </div> -->
                          {{-- Form password yang diperbaiki --}}
<div class="col-md-6">
    <label for="new_password" class="form-label">Ganti Password</label>
    <input
        type="password"
        name="new_password"
        id="new_password"
        class="form-control @error('new_password') is-invalid @enderror"
        placeholder="Kosongkan jika tidak ingin mengganti password"
        readonly
        ondblclick="enablePasswordEdit()"
    >
    @error('new_password')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Klik dua kali untuk mengubah password. Minimal 8 karakter.</small>
</div>

{{-- Konfirmasi password --}}
<div class="col-md-6">
    <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
    <input
        type="password"
        name="new_password_confirmation"
        id="new_password_confirmation"
        class="form-control @error('new_password_confirmation') is-invalid @enderror"
        placeholder="Ulangi password baru"
        readonly
        ondblclick="enablePasswordEdit()"
    >
    @error('new_password_confirmation')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Klik dua kali untuk mengaktifkan konfirmasi password</small>
</div>  
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2">{{ __('menu.general.update') }}</button>
                            <button type="reset" class="btn btn-outline-secondary">{{ __('menu.general.cancel') }}</button>
                        </div>
                    </div>
                    <!-- /Account -->
                </form>
            </div>

            @if(auth()->user()->role == 'staff')
            <div class="card">
                <h5 class="card-header">{{ __('navbar.profile.deactivate_account') }}</h5>
                <div class="card-body">
                    <div class="mb-3 col-12 mb-0">
                        <div class="alert alert-warning">
                            <h6 class="alert-heading fw-bold mb-1">{{ __('navbar.profile.deactivate_confirm_message') }}</h6>
                        </div>
                    </div>
                    <form id="formAccountDeactivation" action="{{ route('profile.deactivate') }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="accountActivation" id="accountActivation">
                            <label class="form-check-label" for="accountActivation">{{ __('navbar.profile.deactivate_confirm') }}</label>
                        </div>
                        <button type="submit" class="btn btn-danger deactivate-account" disabled>{{ __('navbar.profile.deactivate_account') }}</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
@push('script')
<script>
    let passwordEditEnabled = false;

    function enablePasswordEdit() {
        if (!passwordEditEnabled) {
            // Enable both password fields
            const passwordField = document.getElementById('new_password');
            const confirmField = document.getElementById('new_password_confirmation');
            
            if (passwordField && confirmField) {
                passwordField.removeAttribute('readonly');
                confirmField.removeAttribute('readonly');
                
                // Focus on the first password field
                passwordField.focus();
                
                // Change placeholder text to indicate fields are now active
                passwordField.placeholder = "Masukkan password baru";
                confirmField.placeholder = "Konfirmasi password baru";
                
                // Add visual indicator that fields are now editable
                passwordField.classList.add('border-primary');
                confirmField.classList.add('border-primary');
                
                passwordEditEnabled = true;
                
                // Add event listeners for validation
                addPasswordValidation();
            }
        }
    }

    function addPasswordValidation() {
        const passwordField = document.getElementById('new_password');
        const confirmField = document.getElementById('new_password_confirmation');
        
        // Real-time validation for password strength
        passwordField.addEventListener('input', function() {
            if (this.value.length > 0 && this.value.length < 8) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (this.value.length >= 8) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-invalid', 'is-valid');
            }
            
            // Check confirmation match when password changes
            validatePasswordMatch();
        });
        
        // Real-time validation for password confirmation
        confirmField.addEventListener('input', function() {
            validatePasswordMatch();
        });
        
        // Reset fields if both are empty
        passwordField.addEventListener('blur', function() {
            if (this.value === '' && confirmField.value === '') {
                resetPasswordFields();
            }
        });
        
        confirmField.addEventListener('blur', function() {
            if (this.value === '' && passwordField.value === '') {
                resetPasswordFields();
            }
        });
    }

    function validatePasswordMatch() {
        const passwordField = document.getElementById('new_password');
        const confirmField = document.getElementById('new_password_confirmation');
        
        if (confirmField.value !== '' && passwordField.value !== confirmField.value) {
            confirmField.classList.add('is-invalid');
            confirmField.classList.remove('is-valid');
        } else if (confirmField.value !== '' && passwordField.value === confirmField.value) {
            confirmField.classList.remove('is-invalid');
            confirmField.classList.add('is-valid');
        } else {
            confirmField.classList.remove('is-invalid', 'is-valid');
        }
    }

    function resetPasswordFields() {
        const passwordField = document.getElementById('new_password');
        const confirmField = document.getElementById('new_password_confirmation');
        
        if (passwordField.value === '' && confirmField.value === '') {
            passwordField.setAttribute('readonly', true);
            confirmField.setAttribute('readonly', true);
            
            passwordField.placeholder = "Kosongkan jika tidak ingin mengganti password";
            confirmField.placeholder = "Ulangi password baru";
            
            passwordField.classList.remove('border-primary', 'is-valid', 'is-invalid');
            confirmField.classList.remove('border-primary', 'is-valid', 'is-invalid');
            
            passwordEditEnabled = false;
        }
    }

    // Form submission validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const passwordField = document.getElementById('new_password');
                const confirmField = document.getElementById('new_password_confirmation');
                
                // If password is filled but confirmation is empty
                if (passwordField.value !== '' && confirmField.value === '') {
                    e.preventDefault();
                    confirmField.classList.add('is-invalid');
                    confirmField.focus();
                    return false;
                }
                
                // If passwords don't match
                if (passwordField.value !== '' && passwordField.value !== confirmField.value) {
                    e.preventDefault();
                    confirmField.classList.add('is-invalid');
                    confirmField.focus();
                    return false;
                }
            });
        }
    });
</script>
@endpush