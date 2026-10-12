<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Masuk · SyfaBuild</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="login-page">
<div class="login-layout">
    <div class="login-brand-panel">
        <div class="brand brand-light"><span class="brand-mark">S</span><span>SyfaBuild<small>BUILDING SUPPLY</small></span></div>
        <div class="login-message">
            <span class="eyebrow">SMART INVENTORY SYSTEM</span>
            <h1>Persediaan rapi.<br>Bisnis terkendali.</h1>
            <p>Satu sistem untuk mengelola stok, transaksi, dan operasional toko bahan bangunan.</p>
        </div>
        <div class="login-bottom">INVENTORY · SALES · REPORTS</div>
    </div>
    <div class="login-form-panel">
        <form class="login-card" method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <h2>Masuk</h2>
            <p>Gunakan akun yang diberikan owner.</p>

            @error('username')
                <x-alert type="error">{{ $message }}</x-alert>
            @enderror

            <label>Username
                <input name="username" value="{{ old('username') }}" required autofocus autocomplete="username">
            </label>
            <label>Password
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <button class="btn primary wide">Masuk</button>
        </form>
    </div>
</div>
</body>
</html>
