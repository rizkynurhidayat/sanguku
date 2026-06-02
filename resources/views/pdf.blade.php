<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - SanguKu</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #38bdf8;
            padding-bottom: 15px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }

        .subtitle {
            font-size: 12px;
            color: #666;
            margin: 5px 0 0 0;
        }

        .title-report {
            text-align: right;
            float: right;
            margin-top: -45px;
            font-size: 16px;
            font-weight: bold;
            color: #4b5563;
        }

        .clear {
            clear: both;
        }

        /* Summary Cards */
        .summary-container {
            margin-bottom: 30px;
            width: 100%;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            width: 30%;
        }

        .summary-box-title {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .summary-box-value {
            font-size: 16px;
            font-weight: bold;
        }

        .net-positive { color: #10b981; }
        .net-negative { color: #f43f5e; }
        .income-color { color: #10b981; }
        .expense-color { color: #f43f5e; }

        /* Table */
        .table-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 10px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.data-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #cbd5e1;
            font-size: 11px;
            text-transform: uppercase;
        }

        table.data-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }

        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-income {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-expense {
            background-color: #ffe4e6;
            color: #9f1239;
        }

        .footer {
            position: fixed;
            bottom: 30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1 class="logo">SanguKu</h1>
            <p class="subtitle">Asisten Keuangan Mu</p>
        </div>
        <div class="title-report">
            LAPORAN KEUANGAN
            <div style="font-size: 10px; font-weight: normal; margin-top: 5px; color: #94a3b8;">
                Tanggal Ekspor: {{ date('d M Y, H:i') }}
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-container">
        <table class="summary-table">
            <tr>
                <td class="summary-box">
                    <div class="summary-box-title">Pemasukan</div>
                    <div class="summary-box-value income-color">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </div>
                </td>
                <td style="width: 5%"></td>
                <td class="summary-box">
                    <div class="summary-box-title">Pengeluaran</div>
                    <div class="summary-box-value expense-color">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
                </td>
                <td style="width: 5%"></td>
                <td class="summary-box" style="border-color: #38bdf8;">
                    <div class="summary-box-title">Saldo Bersih</div>
                    <div class="summary-box-value {{ $netBalance >= 0 ? 'net-positive' : 'net-negative' }}">
                        Rp {{ number_format($netBalance, 0, ',', '.') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Transaction History Table -->
    <div>
        <div class="table-title">Daftar Transaksi</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 20%">Tanggal</th>
                    <th style="width: 15%">Tipe</th>
                    <th style="width: 45%">Keterangan Transaksi</th>
                    <th style="width: 20%">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $trx)
                    <tr>
                        <td>{{ $trx->transaction_date->format('d M Y, H:i') }}</td>
                        <td>
                            <span class="badge badge-{{ $trx->type }}">
                                {{ $trx->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </span>
                        </td>
                        <td>"{{ $trx->description }}"</td>
                        <td class="{{ $trx->type == 'income' ? 'income-color' : 'expense-color' }}" style="font-weight: bold;">
                            Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Laporan keuangan ini dibuat otomatis oleh aplikasi SanguKu. &copy; {{ date('Y') }} SanguKu. All rights reserved.
    </div>
</body>
</html>
