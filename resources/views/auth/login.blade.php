<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SanguKu - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0b0f19;
            --bg-card: rgba(17, 24, 39, 0.75);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --color-primary: #38bdf8;
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
            align-items: center;
            justify-content: center;
            background-image: 
                radial-gradient(at 0% 0%, rgba(56, 189, 248, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(244, 63, 94, 0.05) 0px, transparent 50%);
            background-attachment: fixed;
            padding: 1rem;
        }

        .auth-container {
            width: 100%;
            max-width: 400px;
            padding: 1.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .logo {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .title {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-group {
            margin-bottom: 1.25rem;
            text-align: left;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            color: var(--text-primary);
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-size: 0.95rem;
            min-height: 44px; /* Touch target optimized */
        }

        .form-control:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2);
        }

        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #38bdf8, #2563eb);
            border: none;
            border-radius: 0.75rem;
            color: white;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 0.75rem;
            min-height: 44px; /* Touch target optimized */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(56, 189, 248, 0.4);
        }

        .error-message {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 0.5rem;
            text-align: left;
        }

        .footer-link {
            margin-top: 1.25rem;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .footer-link a {
            color: var(--color-primary);
            text-decoration: none;
            font-weight: 500;
            min-height: 30px;
            display: inline-block;
        }

        .footer-link a:hover {
            text-decoration: underline;
        }

        /* Screen widths greater than 480px */
        @media (min-width: 480px) {
            .auth-container {
                padding: 2rem;
            }
            .logo {
                font-size: 3rem;
            }
            .title {
                font-size: 1.5rem;
                margin-bottom: 2rem;
            }
            .btn-submit {
                font-size: 1rem;
                margin-top: 1rem;
            }
            .footer-link {
                margin-top: 1.5rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo">💰</div>
        <div class="title">Masuk ke SanguKu</div>

        @if($errors->any())
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; padding: 10px; border-radius: 8px; margin-bottom: 1rem; font-size: 0.85rem;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn-submit">Login</button>
        </form>

        <div class="footer-link">
            Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
        </div>
    </div>
</body>
</html>
