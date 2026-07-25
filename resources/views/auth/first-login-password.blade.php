<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Set Your Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; background: #0B1E3D url("{{ asset('images/bg.png') }}") bottom center / cover no-repeat; font-family: Inter, sans-serif; color: #0B1E3D; display: grid; place-items: center; padding: 24px; }
        .card { width: min(100%, 500px); background: #F0F4F8; border: 1px solid rgba(226,232,240,.6); border-radius: 8px; padding: 64px; box-shadow: 0 20px 60px rgba(0,0,0,.22); }
        h1 { margin: 0 0 16px; font-size: 24px; }
        p { color: #5B7A9D; font-size: 14px; line-height: 1.55; margin: 0 0 28px; }
        label { display: block; font-size: 13px; font-weight: 700; margin: 0 0 8px; }
        input { width: 100%; height: 46px; border: 1px solid #E2E8F0; border-radius: 4px; padding: 0 16px; margin-bottom: 20px; font: inherit; color: #0B1E3D; outline: none; }
        input:focus { border-color: #1B6FC8; box-shadow: 0 0 0 2px rgba(27,111,200,.2); }
        button { width: 100%; height: 48px; border: 0; border-radius: 4px; background: #0B1E3D; color: #fff; font: 700 14px Inter, sans-serif; cursor: pointer; }
        button:hover { background: #132B52; }
        .error { color: #B91C1C; font-size: 12px; font-weight: 600; margin: -12px 0 16px; }
    </style>
</head>
<body>
    <main class="card">
        <h1>Create your password</h1>
        <p>This is your first sign-in. Choose a new password to continue to the HR module.</p>

        <form method="POST" action="{{ route('hr.first-login.password.store') }}">
            @csrf
            <label for="password">New Password</label>
            <input id="password" type="password" name="password" autocomplete="new-password" required>
            @error('password')<div class="error">{{ $message }}</div>@enderror

            <label for="password_confirmation">Confirm New Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>

            <button type="submit">Continue to HR</button>
        </form>
    </main>
</body>
</html>
