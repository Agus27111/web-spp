<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .nota {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid #000;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header h3 {
            margin: 5px 0;
            font-size: 14px;
        }

        .header p {
            margin: 0;
            font-size: 12px;
        }

        .details {
            margin-bottom: 20px;
        }

        .details p {
            margin: 4px 0;
        }

        .separator {
            border-top: 1px dashed #333;
            margin: 20px 0;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .footer p {
            margin: 4px 0;
        }

        .ttd-area {
            text-align: center;
        }

        .ttd-line {
            border-bottom: 1px solid #333;
            width: 150px;
            margin: 50px auto 10px auto;
        }

    </style>
</head>
<body>
    <div class="nota">
        <div class="header">
            <h2>STRUK PEMBAYARAN</h2>
            <h3>{{ $record->foundation->name }}</h3>
            <p>{{ $record->foundation->address }}</p>
        </div>

        <div class="details">
            <p><strong>Siswa:</strong> {{ $record->studentAcademicYear->student->name }}</p>
            <p><strong>NIS:</strong> {{ $record->studentAcademicYear->student->nis }}</p>
            <p><strong>Jenis Pembayaran:</strong> {{ $record->fee->feeType->name }}</p>
            @if ($record->month)
                <p><strong>Bulan:</strong> {{ $record->month }}</p>
            @endif
            <p><strong>Tanggal:</strong> {{ $record->payment_date->format('d/m/Y') }}</p>
            <p><strong>Jumlah:</strong> Rp {{ number_format($record->paid_amount, 0, ',', '.') }}</p>
            <p><strong>Metode:</strong> {{ $record->payment_method }}</p>
        </div>

        <div class="separator"></div>

        <div class="footer">
            <div>
                <p>Terima kasih telah melakukan pembayaran.</p>
            </div>

            <div class="ttd-area">
                <p>Hormat kami,</p>
                <div class="ttd-line"></div>
                <p><strong>{{ $record->foundation->head_name ?? 'Kepala Yayasan' }}</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
