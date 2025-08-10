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
    
    
    
    <style>
        .otp-container {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1.5rem;
}

.otp-input {
    width: 50px;
    height: 60px;
    font-size: 1.5rem;
    text-align: center;
    border-radius: 8px;
    border: 1px solid #dce1e8;
    background-color: white;
    font-weight: 600;
}

.otp-input:focus {
    border-color: #4361ee;
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    outline: none;
}

.timer {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 15px;
    text-align: center;
}
    </style>
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
     <div class="mb-4">
            <label for="otp" class="form-label">Masukkan Kode OTP</label>
            <div class="otp-container">
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
            </div>
            <input type="hidden" name="otp" id="otpValue">
            
            <div class="timer text-center">
                <i class="far fa-clock me-1"></i> Kode berlaku selama <span id="countdown">10:00</span>
            </div>
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
    <script>

document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.otp-input');
    const otpValue = document.getElementById('otpValue');
    const form = document.getElementById('otpForm');

    // Auto focus ke input berikutnya
    inputs.forEach((input, index) => {
        input.addEventListener('input', function () {
            if (this.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            updateOTP();
        });

        // Backspace mundur
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Paste OTP
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text').trim();
            if (/^\d+$/.test(pasteData)) {
                pasteData.split('').forEach((num, i) => {
                    if (inputs[i]) inputs[i].value = num;
                });
                updateOTP();
            }
        });
    });

    function updateOTP() {
        otpValue.value = Array.from(inputs).map(input => input.value).join('');
    }

    // Timer
    let timeLeft = 600; // 10 menit
    const countdownEl = document.getElementById('countdown');

    const timer = setInterval(function () {
        const minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;
        seconds = seconds < 10 ? '0' + seconds : seconds;

        countdownEl.innerHTML = `${minutes}:${seconds}`;

        if (timeLeft <= 0) {
            clearInterval(timer);
            countdownEl.innerHTML = 'Waktu habis';
            inputs.forEach(input => input.disabled = true);
        }
        timeLeft--;
    }, 1000);

    // Validasi sebelum submit
    form.addEventListener('submit', function (e) {
        updateOTP();
        if (otpValue.value.length !== inputs.length) {
            e.preventDefault();
            alert('Lengkapi kode OTP terlebih dahulu!');
        }
    });
});
</script>
