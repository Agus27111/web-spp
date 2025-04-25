<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengajuan Yayasan Baru</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 30px;">
    <div style="max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #333;">📝 Pengajuan Yayasan Baru</h2>
        <p>Halo Admin,</p>
        <p>Seseorang telah mengajukan pendaftaran yayasan melalui website. Berikut detailnya:</p>

        <ul>
            <li><strong>Nama Yayasan:</strong> {{ $foundation->name }}</li>
            <li><strong>Email:</strong> {{ $foundation->email }}</li>
            <li><strong>Nomor Telepon:</strong> {{ $foundation->phone_number }}</li>
            <li><strong>Alamat:</strong> {{ $foundation->address }}</li>
        </ul>

        <p>Silakan login ke dashboard admin untuk memproses pengajuan ini lebih lanjut.</p>

        <p>
            <a href="{{ url('/admin') }}" style="display: inline-block; background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                Buka Dashboard
            </a>
        </p>

        <hr>
        <p>Terima kasih,</p>
        <p><strong>Web-Spp System</strong></p>
    </div>
</body>
</html>
