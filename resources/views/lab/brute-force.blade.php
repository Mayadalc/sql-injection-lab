<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Brute Force Login Lab</title>
    <style>
        body{font-family:system-ui,sans-serif;max-width:760px;margin:2rem auto;padding:0 1rem;color:#1f2937}
        .badge{display:inline-block;padding:.25rem .7rem;border-radius:20px;color:white;font-weight:600}
        .bad{background:#b91c1c}.good{background:#15803d}
        form{display:grid;gap:.7rem;max-width:420px;margin:1.2rem 0}
        input,button{padding:.65rem;font:inherit}button{cursor:pointer}
        .notice{padding:.8rem;border-radius:6px;margin:.8rem 0;background:#f3f4f6}
        .error{background:#fee2e2;color:#7f1d1d}.success{background:#dcfce7;color:#166534}
        code{background:#f3f4f6;padding:.15rem .3rem}
        nav a{margin-right:1rem}
    </style>
</head>
<body>
    <h1>Brute Force Login Lab</h1>
    <nav>
        <a href="/brute-force">Vulnerable login</a>
        <a href="/brute-force/secure">Protected login</a>
        <a href="/vulnerable/search">SQL Injection lab</a>
    </nav>

    @if($mode === 'vulnerable')
        <p><span class="badge bad">Vulnerable: no rate limiting</span></p>
        <p>This educational endpoint accepts repeated login attempts without limiting their frequency.</p>
        <form method="POST" action="/brute-force/login">
    @else
        <p><span class="badge good">Protected: rate limiting enabled</span></p>
        <p>This endpoint allows at most <strong>5 requests per minute per IP</strong>. Further requests receive HTTP 429 (Too Many Requests).</p>
        <form method="POST" action="/brute-force/secure/login">
    @endif
        @csrf
        <label>Email</label>
        <input type="email" name="email" required value="{{ old('email') }}" placeholder="admin@lab.test">
        <label>Password</label>
        <input type="password" name="password" required placeholder="Enter demo password">
        <button type="submit">Try login</button>
    </form>

    @if(session('failure')) <div class="notice error">{{ session('failure') }}</div> @endif
    @if(session('success')) <div class="notice success">{{ session('success') }}</div> @endif
    @if($errors->any())
        <div class="notice error">@foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach</div>
    @endif

    <div class="notice">
        <strong>Local demo account</strong><br>
        Email: <code>admin@lab.test</code><br>
        Password: <code>Admin#2026</code>
    </div>

    <h2>What to observe</h2>
    <ul>
        <li>Vulnerable page: repeated failed attempts are not throttled by this route.</li>
        <li>Protected page: after 5 requests in one minute from the same IP, Laravel returns HTTP 429.</li>
        <li>Both pages use password hashing verification with <code>Hash::check()</code>; the intentionally vulnerable part is the missing attempt limit.</li>
    </ul>
    <p><strong>Defence:</strong> rate limiting, strong unique passwords, MFA, monitoring failed logins, and temporary account protection.</p>
    <p>Use only this local lab and its demo account. Do not test against accounts or systems without explicit permission.</p>
</body>
</html>
