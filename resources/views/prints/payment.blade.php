<!DOCTYPE html>
<html>

<head>
    <title>Struk Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .details {
            margin-bottom: 15px;
        }

        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .ttd-area {
            text-align: center;
            width: 200px;
            margin-left: auto;
        }

        .ttd-line {
            height: 1px;
            background: #000;
            margin: 60px 0 5px;
        }

        .separator {
            border-top: 1px dashed #000;
            margin: 20px 0;
        }
    </style>
</head>

<body>
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
        <div style="width: 60%;">
            <p>Terima kasih telah melakukan pembayaran</p>
        </div>

        <div class="ttd-area">
            <p>Hormat kami,</p>
            <div class="ttd-line"></div>
            <p><strong>{{ $record->foundation->head_name ?? 'Kepala Yayasan' }}</strong></p>
        </div>
    </div>
</body>

</html>
