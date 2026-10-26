<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Akun - Forecasting Academy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #2563EB;
            --cyan: #0EA5E9;
            --text-main: #0F172A;
            --text-sub: #475569;
            --border: #E2E8F0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            min-height: 100vh;
            background: radial-gradient(circle at top left, #EFF6FF, #F8FAFC 60%, #E0F2FE);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            color: var(--text-main);
        }

        .login-card {
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 16px;
            width: 100%;
            max-width: 410px;
            padding: 26px 22px;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
        }

        .brand-head {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text-main);
            margin-bottom: 16px;
        }

        .brand-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--primary), var(--cyan));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.85rem;
        }

        .login-title {
            text-align: center;
            font-size: 1.18rem;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .login-sub {
            text-align: center;
            font-size: 0.78rem;
            color: var(--text-sub);
            margin-bottom: 18px;
        }

        .alert-err {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FECACA;
            padding: 9px 12px;
            border-radius: 9px;
            font-size: 0.76rem;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .form-control {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--border);
            border-radius: 9px;
            font-size: 0.82rem;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--primary), var(--cyan));
            color: #fff;
            font-weight: 700;
            font-size: 0.84rem;
            cursor: pointer;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 14px 0;
            color: #94A3B8;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--border);
        }

        .divider span {
            padding: 0 10px;
        }

        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 9px;
            border: 1px solid var(--border);
            border-radius: 9px;
            background: #fff;
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .demo-box {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px dashed var(--border);
        }

        .demo-box p {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-sub);
            margin-bottom: 8px;
            text-align: center;
        }

        .btn-admin-direct {
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #FECACA;
            background: #FEF2F2;
            color: #991B1B;
            font-size: 0.76rem;
            font-weight: 800;
            cursor: pointer;
            margin-bottom: 8px;
            transition: 0.15s;
        }

        .btn-admin-direct:hover {
            background: #EF4444;
            color: #fff;
        }

        .demo-pills {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
        }

        .demo-pill {
            font-size: 0.68rem;
            font-weight: 700;
            padding: 6px 8px;
            border-radius: 7px;
            border: 1px solid #BFDBFE;
            background: #EFF6FF;
            color: var(--primary);
            cursor: pointer;
        }

        .demo-pill:hover {
            background: var(--primary);
            color: #fff;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 12px;
            font-size: 0.75rem;
            color: var(--text-sub);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <a href="{{ route('beranda') }}" class="brand-head">
            <div class="brand-icon">FA</div>
            <span style="font-weight:800; font-size:1rem;">Forecasting <span
                    style="color:var(--primary)">Academy</span></span>
        </a>

        <h1 class="login-title">Masuk ke Akun Anda</h1>
        <p class="login-sub">Gunakan Email & Password atau Akun Google</p>

        @if ($errors->any())
            <div class="alert-err">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" id="login-form">
            @csrf
            <div class="form-group">
                <label>Alamat Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}"
                    placeholder="contoh@gmail.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" id="password" class="form-control"
                    placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn-submit">Masuk Sekarang</button>
        </form>

        <div class="divider"><span>ATAU</span></div>

        <a href="{{ route('login.google') }}" class="btn-google">
            <svg width="16" height="16" viewBox="0 0 48 48">
                <path fill="#EA4335"
                    d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
                <path fill="#4285F4"
                    d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
                <path fill="#FBBC05"
                    d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
                <path fill="#34A853"
                    d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
            </svg>
            <span>Masuk dengan Google (Gmail)</span>
        </a>

        <div class="demo-box">
            <p>⚡ Akses Cepat Akun Seeder (Otomatis Masuk):</p>
            <button type="button" class="btn-admin-direct" onclick="quickLogin('admin@forecasting.test')">
                🛡️ Masuk Langsung sebagai ADMIN (/control-admin)
            </button>
            <div class="demo-pills">
                <button type="button" class="demo-pill" onclick="quickLogin('andi.student@gmail.com')">👨‍🎓 Peserta 1
                    (Penuh)</button>
                <button type="button" class="demo-pill" onclick="quickLogin('citra.peserta@gmail.com')">👩‍🎓 Peserta 2
                    (VIP)</button>
                <button type="button" class="demo-pill" onclick="quickLogin('dimas.baru@gmail.com')">🆕 Peserta
                    Kosong</button>
                <button type="button" class="demo-pill" onclick="quickLogin('budi.mentor@gmail.com')">👨‍🏫 Akun
                    Mentor</button>
            </div>
        </div>

        <a href="{{ route('beranda') }}" class="back-link">&larr; Kembali ke Beranda</a>
    </div>

    <script>
        function quickLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password123';
            document.getElementById('login-form').submit();
        }
    </script>
</body>

</html>
