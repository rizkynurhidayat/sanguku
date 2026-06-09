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
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            width: auto;
            flex-grow: 1;
        }

        .btn-delete:hover {
            background-color: rgba(239, 68, 68, 0.25);
        }

        .btn-edit {
            background: rgba(56, 189, 248, 0.1);
            border: 1px solid rgba(56, 189, 248, 0.2);
            color: #38bdf8;
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
            width: auto;
            flex-grow: 1;
           
            
        }

        .btn-edit:hover {
            background-color: rgba(56, 189, 248, 0.25);
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
                margin-left: 0px;
                min-height: auto;
                width: auto;
                display: inline-block;
            }

            .btn-delete:hover {
                background-color: rgba(239, 68, 68, 0.1);
            }

            .btn-edit {
                background: transparent;
                border: none;
                color: var(--color-primary);
                padding: 0.25rem 0.5rem;
                border-radius: 0.375rem;
                font-size: 0.85rem;
                min-height: auto;
                width: auto;
                display: inline-block;
                cursor: pointer;
            }

            .btn-edit:hover {
                background-color: var(--color-primary-glow);
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
        }

        /* Category Chart Styles */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
            align-items: start;
        }

        @media (min-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1.2fr 0.8fr;
            }
        }

        .chart-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            padding: 1.5rem;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .chart-container {
            position: relative;
            width: 100%;
            max-width: 200px;
            height: 200px;
            margin: 1rem auto 1.5rem auto;
        }

        .chart-legend {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .legend-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            padding: 0.65rem 0.85rem;
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.04);
        }

        .legend-label-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .legend-color-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .legend-value-group {
            text-align: right;
        }

        .legend-percentage {
            font-weight: 600;
        }

        .legend-target {
            font-size: 0.7rem;
            color: var(--text-secondary);
            display: block;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(11, 15, 25, 0.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-content {
            width: 90%;
            max-width: 500px;
            background: rgba(17, 24, 39, 0.95);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            padding: 1.75rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }

        .modal-overlay.show .modal-content {
            transform: scale(1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.75rem;
        }

        .modal-close-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            font-size: 1.25rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .modal-close-btn:hover {
            color: var(--text-primary);
        }

        .form-group {
            margin-bottom: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            text-align: left;
        }

        .form-group label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .form-group input, 
        .form-group select {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--text-primary);
            font-family: var(--font-main);
            font-size: 0.9rem;
            outline: none;
            transition: all 0.2s;
        }

        .form-group input:focus, 
        .form-group select:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 10px var(--color-primary-glow);
            background: rgba(255, 255, 255, 0.05);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        @media (min-width: 500px) {
            .form-row {
                grid-template-columns: 1fr 1fr;
            }
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.75rem;
            border-top: 1px solid var(--border-color);
            padding-top: 1.25rem;
        }

        .btn-cancel {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            padding: 0.65rem 1.25rem;
            border-radius: 0.75rem;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
        }

        .btn-save {
            background: linear-gradient(135deg, #38bdf8, #2563eb);
            border: none;
            color: white;
            padding: 0.65rem 1.5rem;
            border-radius: 0.75rem;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(56, 189, 248, 0.3);
            transition: all 0.2s;
        }

        .btn-save:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(56, 189, 248, 0.5);
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

        <!-- Dashboard Grid: Recorder & Category Allocation Chart -->
        <div class="dashboard-grid">
            <!-- Voice Recorder Component -->
            <section class="recorder-section" style="margin-bottom: 0; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; box-sizing: border-box; padding: 2rem 1.25rem;">
                <div class="recorder-title">Catat Transaksi Baru</div>
                <div class="recorder-subtitle">Ketuk mikrofon di bawah dan katakan transaksi Anda. Contoh: <br><strong>"Membeli bensin 20000"</strong> atau <strong>"Mendapatkan gaji bulanan 5000000"</strong>. pastikan untuk <strong>"DOUBLE CHECK"</strong> hasilnya.</div>
                
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

                <button id="recordBtn" class="btn-record" style="margin-bottom: 0;">
                    <span id="recordIcon">🎙️</span>
                </button>
            </section>

            <!-- Category Chart Section -->
            <section class="chart-section" style="height: 100%; box-sizing: border-box; padding: 2rem 1.25rem;">
                <h2 class="section-title" style="margin-bottom: 0.5rem; font-size: 1.15rem; font-weight: 600;">Alokasi Pengeluaran</h2>
                <p style="font-size: 0.8rem; color: var(--text-secondary); text-align: center; margin-bottom: 0.5rem;">
                    Pantau target alokasi anggaran (50/30/20) Anda.
                </p>
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="chart-legend">
                    <div class="legend-item">
                        <div class="legend-label-group">
                            <span class="legend-color-dot" style="background-color: #38bdf8;"></span>
                            <div>
                                <div style="font-weight: 500;">Needs (Kebutuhan)</div>
                                <span class="legend-target">Target: 50%</span>
                            </div>
                        </div>
                        <div class="legend-value-group">
                            <div class="legend-percentage" id="needsPercent">0%</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Rp {{ number_format($needsSum, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-label-group">
                            <span class="legend-color-dot" style="background-color: #f43f5e;"></span>
                            <div>
                                <div style="font-weight: 500;">Wants (Keinginan)</div>
                                <span class="legend-target">Target: 30%</span>
                            </div>
                        </div>
                        <div class="legend-value-group">
                            <div class="legend-percentage" id="wantsPercent">0%</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Rp {{ number_format($wantsSum, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-label-group">
                            <span class="legend-color-dot" style="background-color: #10b981;"></span>
                            <div>
                                <div style="font-weight: 500;">Savings (Investasi)</div>
                                <span class="legend-target">Target: 20%</span>
                            </div>
                        </div>
                        <div class="legend-value-group">
                            <div class="legend-percentage" id="savingsPercent">0%</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Rp {{ number_format($savingsSum, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    @if($otherSum > 0)
                        <div class="legend-item">
                            <div class="legend-label-group">
                                <span class="legend-color-dot" style="background-color: #9ca3af;"></span>
                                <div>
                                    <div style="font-weight: 500;">Lainnya</div>
                                    <span class="legend-target">Tanpa Kategori</span>
                                </div>
                            </div>
                            <div class="legend-value-group">
                                <div class="legend-percentage" id="otherPercent">0%</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">Rp {{ number_format($otherSum, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        </div>

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
                                <th>Kategori</th>
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
                                    <td data-label="Kategori">
                                        @if($trx->category_group)
                                            <div style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary);">{{ $trx->sub_category }}</div>
                                            <span class="badge" style="font-size: 0.65rem; padding: 0.15rem 0.4rem; background: {{ $trx->category_group == 'Needs' ? 'var(--color-primary-glow)' : ($trx->category_group == 'Wants' ? 'var(--color-expense-glow)' : 'var(--color-income-glow)') }}; color: {{ $trx->category_group == 'Needs' ? 'var(--color-primary)' : ($trx->category_group == 'Wants' ? 'var(--color-expense)' : 'var(--color-income)') }}; border: 1px solid rgba(255, 255, 255, 0.05);">
                                                {{ $trx->category_group }}
                                            </span>
                                        @else
                                            <span style="opacity: 0.5;">-</span>
                                        @endif
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
                                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end; ">
                                            <button type="button" class="btn-edit" onclick="openEditModal({{ json_encode($trx) }})" title="Edit Transaksi">✏️ Edit</button>
                                            <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')" style="margin: 0; display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-delete" title="Hapus Transaksi">🗑️ Hapus</button>
                                            </form>
                                        </div>
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

    <!-- Edit Transaction Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem; font-weight: 600; background: linear-gradient(to right, #38bdf8, #818cf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Edit Transaksi</h3>
                <button type="button" class="modal-close-btn" onclick="closeEditModal()">✕</button>
            </div>
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="editDescription">Keterangan Transaksi</label>
                    <input type="text" id="editDescription" name="description" placeholder="Contoh: Beli nasi goreng" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="editAmount">Nominal (Rp)</label>
                        <input type="number" id="editAmount" name="amount" placeholder="0" required min="1">
                    </div>
                    
                    <div class="form-group">
                        <label for="editType">Tipe Transaksi</label>
                        <select id="editType" name="type" required>
                            <option value="expense">Pengeluaran</option>
                            <option value="income">Pemasukan</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="editCategoryGroup">Kelompok Anggaran</label>
                        <select id="editCategoryGroup" name="category_group" onchange="updateSubCategoryOptions()">
                            <option value="Needs">Needs (Kebutuhan Utama)</option>
                            <option value="Wants">Wants (Keinginan)</option>
                            <option value="Savings">Savings (Investasi)</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="editSubCategory">Sub Kategori</label>
                        <select id="editSubCategory" name="sub_category">
                            <!-- Populated dynamically via JS -->
                        </select>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn-save">Simpan</button>
                </div>
            </form>
        </div>
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

        // Chart.js Category Allocation Initialization
        document.addEventListener('DOMContentLoaded', function() {
            const needsSum = {{ $needsSum }};
            const wantsSum = {{ $wantsSum }};
            const savingsSum = {{ $savingsSum }};
            const otherSum = {{ $otherSum }};
            const totalExpense = needsSum + wantsSum + savingsSum + otherSum;

            // Calculate percentages dynamically
            if (totalExpense > 0) {
                document.getElementById('needsPercent').textContent = ((needsSum / totalExpense) * 100).toFixed(1) + '%';
                document.getElementById('wantsPercent').textContent = ((wantsSum / totalExpense) * 100).toFixed(1) + '%';
                document.getElementById('savingsPercent').textContent = ((savingsSum / totalExpense) * 100).toFixed(1) + '%';
                const otherEl = document.getElementById('otherPercent');
                if (otherEl) {
                    otherEl.textContent = ((otherSum / totalExpense) * 100).toFixed(1) + '%';
                }
            }

            const ctx = document.getElementById('categoryChart').getContext('2d');
            
            let chartData, chartLabels, chartColors;
            
            if (totalExpense === 0) {
                // Empty state dataset
                chartData = [1];
                chartLabels = ['Belum ada pengeluaran'];
                chartColors = ['rgba(156, 163, 175, 0.15)'];
            } else {
                chartData = [needsSum, wantsSum, savingsSum, otherSum];
                chartLabels = ['Needs (Kebutuhan)', 'Wants (Keinginan)', 'Savings (Investasi)', 'Lainnya'];
                chartColors = [
                    '#38bdf8', // Emerald Blue
                    '#f43f5e', // Rose
                    '#10b981', // Emerald
                    '#9ca3af'  // Gray
                ];
            }
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartData,
                        backgroundColor: chartColors,
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (totalExpense === 0) return 'Belum ada data';
                                    const value = context.raw;
                                    const percentage = ((value / totalExpense) * 100).toFixed(1);
                                    return `${context.label}: Rp ${new Intl.NumberFormat('id-ID').format(value)} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        });

        // Sub-categories Mapping for Edit Modal
        const subCategoriesMap = {
            'Needs': [
                'Makan & Minum',
                'Transportasi & Kendaraan',
                'Tagihan & Kebutuhan Rumah/Kos',
                'Pendidikan / Kerja'
            ],
            'Wants': [
                'Nongkrong & Hiburan',
                'Belanja & Self-Reward',
                'Sosial & Jajan'
            ],
            'Savings': [
                'Tabungan & Investasi',
                'Dana Darurat'
            ],
            'Lainnya': [
                'Belum Dikategorikan'
            ]
        };

        const editModal = document.getElementById('editModal');
        const editForm = document.getElementById('editForm');
        const editDescription = document.getElementById('editDescription');
        const editAmount = document.getElementById('editAmount');
        const editType = document.getElementById('editType');
        const editCategoryGroup = document.getElementById('editCategoryGroup');
        const editSubCategory = document.getElementById('editSubCategory');

        function openEditModal(transaction) {
            editDescription.value = transaction.description;
            editAmount.value = Math.round(transaction.amount);
            editType.value = transaction.type;
            editCategoryGroup.value = transaction.category_group || 'Lainnya';
            
            // Set action URL dynamically
            editForm.action = `/transactions/${transaction.id}`;
            
            // Populate sub-category dropdown based on the category group
            updateSubCategoryOptions(transaction.sub_category);
            
            // Show modal
            editModal.classList.add('show');
        }

        function closeEditModal() {
            editModal.classList.remove('show');
        }

        function updateSubCategoryOptions(selectedSubCategory = null) {
            const group = editCategoryGroup.value;
            const subs = subCategoriesMap[group] || [];
            
            // Clear current options
            editSubCategory.innerHTML = '';
            
            // Populate options
            subs.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub;
                option.textContent = sub;
                if (selectedSubCategory && sub.toLowerCase() === selectedSubCategory.toLowerCase()) {
                    option.selected = true;
                }
                editSubCategory.appendChild(option);
            });
        }
    </script>
</body>
</html>
