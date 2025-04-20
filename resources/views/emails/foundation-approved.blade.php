<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Pendaftaran Disetujui</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 30px;">
    <div
        style="max-width: 600px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #4CAF50;">Selamat 🎉</h2>
        <p>Halo {{ $record->name }},</p>

        <p>Pengajuan pendaftaran yayasan Anda telah <strong>disetujui</strong> oleh admin.</p>
        <p>Silakan login ke dashboard admin untuk mulai mengelola yayasan Anda.</p>

        <a href="{{ url('/login') }}"
            style="display: inline-block; background-color: #4CAF50; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; cursor: pointer;">
            Masuk ke Dashboard
        </a>


        <p>Gunakan email: <strong>{{ $record->email }}</strong> dan password default:
            <strong>{{ $password }}</strong>
        </p>
        <p><em>Disarankan untuk segera mengganti password setelah login.</em></p>

        <hr>

        <p>Terima kasih,</p>
        <p><strong>Tim Admin Web-Spp</strong></p>
    </div>
</body>

</html>
