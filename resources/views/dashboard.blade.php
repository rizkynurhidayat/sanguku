<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SanguKu - Pencatat Keuangan Suara</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0b0f19;
            --bg-card: rgba(17, 24, 39, 0.75);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --color-primary: #38bdf8;
            --color-primary-glow: rgba(56, 189, 248, 0.15);
            --color-income: #10b981;
            --color-income-glow: rgba(16, 185, 129, 0.15);
            --color-expense: #f43f5e;
            --color-expense-glow: rgba(244, 63, 94, 0.15);
            --font-main: 'Outfit', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: 
                radial-gradient(at 0% 0%, rgba(56, 189, 248, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(244, 63, 94, 0.05) 0px, transparent 50%);
            background-attachment: fixed;
        }

        /* Container: 1rem padding on Mobile for extra room */
        .container {
            max-width: 1000px;
            width: 100%;
            margin: 0 auto;
            padding: 1.25rem 1rem;
        }

        /* Header: Flex direction changes to column-reverse or adjusts for mobile */
        header {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-icon {
            font-size: 2rem;
        }

        .logo h1 {
            font-size: 1.6rem;
            font-weight: 700;
            background: linear-gradient(to right, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo p {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: space-between;
            width: 100%;
        }

        .user-greeting {
            text-align: left;
        }

        .user-greeting .greet-text {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .user-greeting .user-name {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .header-buttons {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Summary Cards - Mobile default: stacked */
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 1.25rem;
            padding: 1.25rem;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card-title {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-value {
            font-size: 1.6rem;
            font-weight: 700;
        }

        .card-net {
            border-left: 4px solid var(--color-primary);
            box-shadow: 0 4px 20px var(--color-primary-glow);
        }
        .card-income {
            border-left: 4px solid var(--color-income);
            box-shadow: 0 4px 20px var(--color-income-glow);
        }
        .card-expense {
            border-left: 4px solid var(--color-expense);
            box-shadow: 0 4px 20px var(--color-expense-glow);
        }

        /* Voice Recorder Section */
        .recorder-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            padding: 2rem 1.25rem;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .recorder-title {
            font-size: 1.15rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .recorder-subtitle {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.4;
        }

        .btn-record {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, #38bdf8, #2563eb);
            color: white;
            font-size: 2.2rem;
            cursor: pointer;
            outline: none;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem auto;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 25px rgba(56, 189, 248, 0.4);
            /* area sentuh terjamin */
            min-width: 48px;
            min-height: 48px;
        }

        .btn-record:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 30px rgba(56, 189, 248, 0.6);
        }

        .btn-record.recording {
            background: linear-gradient(135deg, #f43f5e, #be123c);
            animation: pulse 1.5s infinite;
            box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.7);
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.7);
            }
            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 15px rgba(244, 63, 94, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(244, 63, 94, 0);
            }
        }

        /* Waves Visualizer */
        .visualizer-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            height: 35px;
            margin-bottom: 1.25rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .visualizer-container.active {
            opacity: 1;
        }

        .wave-bar {
            width: 4px;
            height: 6px;
            background-color: var(--color-expense);
            border-radius: 4px;
            transition: height 0.15s ease;
        }

        .visualizer-container.active .wave-bar {
            animation: bounce 0.8s ease-in-out infinite alternate;
        }

        .wave-bar:nth-child(2n) { background-color: var(--color-primary); animation-delay: 0.1s !important; }
        .wave-bar:nth-child(3n) { background-color: #818cf8; animation-delay: 0.2s !important; }
        .wave-bar:nth-child(4n) { background-color: var(--color-income); animation-delay: 0.3s !important; }
        .wave-bar:nth-child(5n) { background-color: #f59e0b; animation-delay: 0.4s !important; }

        @keyframes bounce {
            0% { height: 6px; }
            100% { height: 30px; }
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            margin-bottom: 1rem;
            color: var(--text-secondary);
        }

        .status-badge.recording {
            color: var(--color-expense);
            border-color: rgba(244, 63, 94, 0.3);
            background: var(--color-expense-glow);
        }

        .status-badge.processing {
            color: var(--color-primary);
            border-color: rgba(56, 189, 248, 0.3);
            background: var(--color-primary-glow);
        }

        .status-badge.success {
            color: var(--color-income);
            border-color: rgba(16, 185, 129, 0.3);
            background: var(--color-income-glow);
        }

        /* History Table Section */
        .history-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            padding: 1.5rem 1.25rem;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .section-title {
            font-size: 1.15rem;
            font-weight: 600;
        }

        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.1rem;
            border-radius: 0.75rem;
            border: 1px solid var(--color-primary);
            background: var(--color-primary-glow);
            color: var(--color-primary);
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            min-height: 44px; /* Touch target size */
            justify-content: center;
        }

        .btn-export:hover {
            background: var(--color-primary);
            color: var(--bg-primary);
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.3);
        }

        /* Mobile-First Layout: Table as Cards */
        .table-responsive {
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            display: block;
        }

        thead {
            display: none; /* Hide header on mobile */
        }

        tbody {
            display: block;
            width: 100%;
        }

        tr {
            display: block;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-radius: 0.85rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            font-size: 0.9rem;
        }

        td:last-child {
            border-bottom: none;
            padding-top: 0.75rem;
        }

        /* Label helper for card items on mobile */
        td::before {
            content: attr(data-label);
            font-weight: 500;
            color: var(--text-secondary);
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-income {
            background: var(--color-income-glow);
            color: var(--color-income);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .badge-expense {
            background: var(--color-expense-glow);
            color: var(--color-expense);
            border: 1px solid rgba(244, 63, 94, 0.2);
        }

        .value-income {
            color: var(--color-income);
            font-weight: 600;
        }

        .value-expense {
            color: var(--color-expense);
            font-weight: 600;
        }

        .desc-text {
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: var(--text-secondary);
            font-style: italic;
            text-align: right;
        }

        .date-text {
            color: var(--text-secondary);
            font-size: 0.85rem;
            text-align: right;
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            font-weight: 500;
            transition: background-color 0.2s;
            min-height: 44px; /* Touch target minimum */
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .btn-delete:hover {
            background-color: rgba(239, 68, 68, 0.25);
        }

        .empty-state {
            padding: 2.5rem 1rem;
            text-align: center;
            color: var(--text-secondary);
        }

        .empty-icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            opacity: 0.5;
        }

        /* Toast Alert */
        .toast {
            position: fixed;
            bottom: 1.5rem;
            left: 1rem;
            right: 1rem;
            padding: 1rem;
            background: #1f2937;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 1000;
            transform: translateY(150%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .toast.show {
            transform: translateY(0);
        }

        .toast-success { border-left: 4px solid var(--color-income); }
        .toast-error { border-left: 4px solid var(--color-expense); }

        .toast-message {
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Breakpoint: Desktop (Tablet/PC) */
        @media (min-width: 768px) {
            .container {
                padding: 2rem 1.5rem;
            }

            header {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2.5rem;
            }

            .logo h1 {
                font-size: 1.75rem;
            }

            .logo p {
                font-size: 0.85rem;
            }

            .header-actions {
                width: auto;
                justify-content: flex-end;
                gap: 1.5rem;
            }

            .user-greeting {
                text-align: right;
            }

            .user-greeting .user-name {
                font-size: 1rem;
            }

            .summary-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 1.5rem;
                margin-bottom: 2.5rem;
            }

            .card {
                padding: 1.5rem;
            }

            .card-title {
                font-size: 0.9rem;
                margin-bottom: 0.5rem;
            }

            .card-value {
                font-size: 1.85rem;
            }

            .recorder-section {
                padding: 2.5rem;
                margin-bottom: 2.5rem;
            }

            .recorder-title {
                font-size: 1.25rem;
            }

            .recorder-subtitle {
                font-size: 0.9rem;
                margin-bottom: 2rem;
            }

            .btn-record {
                width: 100px;
                height: 100px;
                font-size: 2.5rem;
                margin-bottom: 1.5rem;
            }

            .visualizer-container {
                height: 40px;
                margin-bottom: 1.5rem;
            }

            .wave-bar {
                width: 4px;
                height: 8px;
            }

            @keyframes bounce {
                0% { height: 8px; }
                100% { height: 35px; }
            }

            .status-badge {
                font-size: 0.85rem;
            }

            .history-section {
                padding: 2rem;
            }

            .section-header {
                margin-bottom: 1.5rem;
            }

            .section-title {
                font-size: 1.25rem;
            }

            .btn-export {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
                min-height: auto;
                width: auto;
            }

            /* Reset Table from Cards to Desktop Table */
            table {
                display: table;
            }

            thead {
                display: table-header-group;
            }

            tbody {
                display: table-row-group;
            }

            tr {
                display: table-row;
                background: transparent;
                border: none;
                box-shadow: none;
            }

            tr:last-child td {
                border-bottom: none;
            }

            th, td {
                display: table-cell;
                padding: 1rem;
                border-bottom: 1px solid var(--border-color);
                width: auto;
            }

            th {
                color: var(--text-secondary);
                font-weight: 500;
                font-size: 0.85rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            td {
                padding: 1.25rem 1rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.03);
                vertical-align: middle;
            }

            td::before {
                display: none;
            }

            .desc-text {
                max-width: 320px;
                text-align: left;
            }

            .date-text {
                text-align: left;
            }

            .btn-delete {
                background: transparent;
                border: none;
                color: #ef4444;
                padding: 0.25rem 0.5rem;
                border-radius: 0.375rem;
                font-size: 0.85rem;
                min-height: auto;
                width: auto;
                display: inline-block;
            }

            .btn-delete:hover {
                background-color: rgba(239, 68, 68, 0.1);
            }

            .toast {
                left: auto;
                right: 2rem;
                bottom: 2rem;
                width: auto;
                padding: 1rem 1.5rem;
            }

            .toast-message {
                font-size: 0.9rem;
            }
        }    font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <div class="logo">
                <span class="logo-icon">💰</span>
                <div>
                    <h1>SanguKu</h1>
                    <p>Asisten Keuangan Berbasis Suara</p>
                </div>
            </div>
            
            <div class="header-actions">
                <div class="user-greeting">
                    <div class="greet-text">Halo,</div>
                    <div class="user-name">{{ Auth::user()->name }}</div>
                </div>
                
                <div class="header-buttons">
                    @if(count($transactions) > 0)
                        <a href="{{ route('transactions.export') }}" class="btn-export">
                            <span>📥</span> Export PDF
                        </a>
                    @endif
                    
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-export" style="background: rgba(244, 63, 94, 0.1); color: var(--color-expense); border-color: rgba(244, 63, 94, 0.3);">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Summary Statistics -->
        <section class="summary-grid">
            <div class="card card-net">
                <div class="card-title"><span>💳</span> Saldo Bersih</div>
                <div class="card-value" style="color: {{ $netBalance >= 0 ? 'var(--color-income)' : 'var(--color-expense)' }}">
                    Rp {{ number_format($netBalance, 0, ',', '.') }}
                </div>
            </div>
            <div class="card card-income">
                <div class="card-title"><span>📈</span> Pemasukan</div>
                <div class="card-value value-income">
                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </div>
            </div>
            <div class="card card-expense">
                <div class="card-title"><span>📉</span> Pengeluaran</div>
                <div class="card-value value-expense">
                    Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </div>
            </div>
        </section>

        <!-- Voice Recorder Component -->
        <section class="recorder-section">
            <div class="recorder-title">Catat Transaksi Baru</div>
            <div class="recorder-subtitle">Ketuk mikrofon di bawah dan katakan transaksi Anda. Contoh: <br><strong>"Membeli bensin 20000"</strong> atau <strong>"Mendapatkan gaji bulanan 5000000"</strong>.</div>
            
            <div id="statusBadge" class="status-badge">Menunggu instruksi...</div>

            <!-- Wave Visualizer Simulation -->
            <div id="visualizer" class="visualizer-container">
                <div class="wave-bar" style="animation-duration: 0.4s"></div>
                <div class="wave-bar" style="animation-duration: 0.5s"></div>
                <div class="wave-bar" style="animation-duration: 0.3s"></div>
                <div class="wave-bar" style="animation-duration: 0.6s"></div>
                <div class="wave-bar" style="animation-duration: 0.4s"></div>
                <div class="wave-bar" style="animation-duration: 0.5s"></div>
                <div class="wave-bar" style="animation-duration: 0.3s"></div>
                <div class="wave-bar" style="animation-duration: 0.4s"></div>
            </div>

            <button id="recordBtn" class="btn-record">
                <span id="recordIcon">🎙️</span>
            </button>
        </section>

        <!-- History Table -->
        <section class="history-section">
            <div class="section-header">
                <h2 class="section-title">Riwayat Transaksi</h2>
            </div>

            <div class="table-responsive">
                @if(count($transactions) > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Keterangan (Hasil Suara)</th>
                                <th>Nominal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="transactionList">
                            @foreach($transactions as $trx)
                                <tr>
                                    <td data-label="Tanggal">
                                        <div class="date-text">
                                            {{ $trx->transaction_date->format('d M Y') }}
                                            <br>
                                            <span style="font-size: 0.75rem; opacity: 0.6;">{{ $trx->transaction_date->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    <td data-label="Tipe">
                                        <span class="badge badge-{{ $trx->type }}">
                                            {{ $trx->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                        </span>
                                    </td>
                                    <td data-label="Keterangan">
                                        <div class="desc-text" title="{{ $trx->description }}">
                                            "{{ $trx->description }}"
                                        </div>
                                    </td>
                                    <td data-label="Nominal">
                                        <span class="value-{{ $trx->type }}">
                                            Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td data-label="Aksi">
                                        <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')" style="width: 100%;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete" title="Hapus Transaksi">🗑️ Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">📂</div>
                        <p>Belum ada transaksi. Silakan rekam suara Anda untuk menambahkan!</p>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <!-- Toast Alert Container -->
    <div id="toast" class="toast">
        <span id="toastIcon"></span>
        <span id="toastMessage" class="toast-message"></span>
    </div>

    <!-- Script Recording Logic -->
    <script>
        let mediaRecorder;
        let audioChunks = [];
        let isRecording = false;

        const recordBtn = document.getElementById('recordBtn');
        const recordIcon = document.getElementById('recordIcon');
        const statusBadge = document.getElementById('statusBadge');
        const visualizer = document.getElementById('visualizer');
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');

        function showToast(message, type = 'success') {
            toastMessage.textContent = message;
            toast.className = 'toast show toast-' + type;
            toastIcon.textContent = type === 'success' ? '✅' : '❌';
            setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        }

        recordBtn.addEventListener('click', async () => {
            if (!isRecording) {
                // Start recording
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });
                    audioChunks = [];

                    mediaRecorder.ondataavailable = (event) => {
                        audioChunks.push(event.data);
                    };

                    mediaRecorder.onstop = async () => {
                        const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                        await uploadAudio(audioBlob);
                    };

                    mediaRecorder.start();
                    isRecording = true;
                    recordBtn.classList.add('recording');
                    recordIcon.textContent = '⏹️';
                    statusBadge.textContent = 'Mendengarkan... Bicara sekarang!';
                    statusBadge.className = 'status-badge recording';
                    visualizer.classList.add('active');

                } catch (err) {
                    console.error("Microphone access denied: ", err);
                    showToast("Tidak bisa mengakses mikrofon. Izinkan mikrofon browser Anda.", "error");
                }
            } else {
                // Stop recording
                mediaRecorder.stop();
                // Stop microphone stream tracks
                mediaRecorder.stream.getTracks().forEach(track => track.stop());
                
                isRecording = false;
                recordBtn.classList.remove('recording');
                recordIcon.textContent = '🎙️';
                statusBadge.textContent = 'Memproses rekaman...';
                statusBadge.className = 'status-badge processing';
                visualizer.classList.remove('active');
            }
        });

        async function uploadAudio(blob) {
            const formData = new FormData();
            formData.append('audio', blob, 'recording.webm');

            try {
                const response = await fetch("{{ route('transactions.voice') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSR-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    statusBadge.textContent = 'Transaksi berhasil disimpan!';
                    statusBadge.className = 'status-badge success';
                    showToast("Transaksi berhasil ditambahkan!", "success");
                    
                    // Reload page after a delay to show updated list and balances
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    statusBadge.textContent = 'Gagal mencatat transaksi.';
                    statusBadge.className = 'status-badge';
                    showToast(result.message || 'Terjadi kesalahan pemrosesan.', 'error');
                }

            } catch (err) {
                console.error("Upload error: ", err);
                statusBadge.textContent = 'Terjadi kesalahan jaringan.';
                statusBadge.className = 'status-badge';
                showToast('Gagal mengirim audio ke server.', 'error');
            }
        }
    </script>
</body>
</html>
