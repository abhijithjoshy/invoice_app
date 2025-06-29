<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container min-vh-100 d-flex flex-column justify-content-center align-items-center">
        <div class="card shadow p-4 mt-5 w-100" style="max-width: 400px;">
            <h2 class="mb-4 text-center">Welcome</h2>
            <div class="d-flex flex-column gap-3">
                <a href="{{ route('login') }}" class="btn btn-primary w-100">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">Register</a>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
