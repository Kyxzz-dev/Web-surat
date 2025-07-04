    <!DOCTYPE html>
    <html lang="en" class="light-style customizer-hide" dir="ltr"
        data-theme="theme-default"
        data-assets-path="{{ asset('sneat/') }}"
        data-template="vertical-menu-template-free">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
        <title>Verifikasi OTP | {{ config('app.name') }}</title>
        <meta name="description" content="Verifikasi OTP"/>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('sneat/img/favicon/favicon.ico') }}"/>

        <!-- Fonts dan CSS -->
        <link rel="preconnect" href="https://fonts.googleapis.com"/>
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700&display=swap" rel="stylesheet"/>
        <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/boxicons.css') }}"/>
        <link rel="stylesheet" href="{{ asset('sneat/vendor/css/core.css') }}"/>
        <link rel="stylesheet" href="{{ asset('sneat/vendor/css/theme-default.css') }}"/>
        <link rel="stylesheet" href="{{ asset('sneat/css/demo.css') }}"/>
        <link rel="stylesheet" href="{{ asset('sneat/vendor/css/pages/page-auth.css') }}"/>
    </head>

    <body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="{{ route('home') }}" class="app-brand-link gap-2">
                                <img src="{{ asset('logo-black.png') }}" alt="{{ config('app.name') }}" width="75px">
                            </a>
                        </div>

                        <h4 class="mb-2 text-center">Verifikasi OTP</h4>
                        <p class="mb-4 text-center">Silakan masukkan kode OTP yang dikirim ke email Anda.</p>
                        <!-- pesan eror -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        <!-- pesan sukses -->
                         @if (session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
                        <!-- pesan otp -->
                        @if (session('otp_user_id'))
                        <form method="POST" action="{{ route('otp.verify') }}">
    @csrf
    <div class="mb-3">
        <x-input-form name="otp" type="text" label="Kode OTP" placeholder="Silahkan Masukkan Kode OTP" />
    </div>

    <button type="submit" class="btn btn-primary d-grid w-100">Verifikasi</button>
</form>
                          <div class="text-center mt-3">
    <form method="POST" action="{{ route('otp.resend') }}">
        @csrf
        <button type="submit" class="btn btn-outline-secondary btn-sm">Kirim Ulang OTP</button>
    </form>
</div>

                        @else
                            <div class="alert alert-warning mt-3">
                                Sesi tidak valid. Silakan daftar ulang.
                            </div>
                        @endif

                    </div>
                </div>
                <!-- /Card -->
            </div>
        </div>
    </div>
    </body>
    </html>
