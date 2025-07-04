<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Kode OTP</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f6f6f6; padding: 30px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <h2 style="color: #333333;">Halo,</h2>
        <p style="color: #555555;">
            Terima kasih telah mendaftar di <strong>{{ config('app.name') }}</strong>.
            Berikut adalah kode OTP untuk verifikasi akun Anda:
        </p>
        <div style="text-align: center; margin: 30px 0;">
            <span style="display: inline-block; background-color: #f0f0f0; padding: 15px 30px; font-size: 24px; font-weight: bold; letter-spacing: 4px; color: #333333; border-radius: 5px;">
                {{ $otp }}
            </span>
        </div>
        <p style="color: #555555;">
            Kode ini berlaku selama 10 menit. Jika Anda tidak merasa melakukan pendaftaran, abaikan email ini.
        </p>
        <p style="color: #777777; font-size: 14px;">Salam,<br>Tim {{ config('app.name') }}</p>
    </div>
</body>
</html>
