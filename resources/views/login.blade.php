<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        body{
            background: linear-gradient(135deg, #1e293b, #0f172a);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card{
            background: #111827;
            color: #fff;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }
        .form-control{
            background: #020617;
            border: 1px solid #334155;
            color: #fff;
        }
        .form-control:focus{
            background: #020617;
            border-color: #0d6efd;
            color: #fff;
            box-shadow: none;
        }
        .btn-login{
            background: #0d6efd;
            border: none;
        }
        .btn-login:hover{
            background: #2563eb;
        }
    </style>
</head>
<body>

<div class="col-md-4">
    <div class="card login-card p-4">
        <div class="text-center mb-4">
            <i class="fa-solid fa-user-shield fa-3x text-primary"></i>
            <h4 class="mt-2">Admin Login</h4>
            <small class="text-secondary">Sign in to continue</small>
        </div>

        <form method="POST" action="{{route('signup')}}">
            @csrf
            @if(session()->has('error'))
                <div class="alert alert-danger">
                    {{session()->get('error')}}
                </div>
            @endif
            <div class="mb-3">
                <label class="form-label">
                    <i class="fa-solid fa-envelope me-1"></i> Email
                </label>
                <input type="email" name="email" value="{{old('email')}}" class="form-control" placeholder="admin@example.com">
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label">
                    <i class="fa-solid fa-lock me-1"></i> Password
                </label>
                <input type="password" name="password" class="form-control" placeholder="••••••••">
            </div>

            <!-- Button -->
            <button class="btn btn-login w-100 text-white">
                <i class="fa-solid fa-right-to-bracket me-1"></i> Login
            </button>
        </form>
    </div>
</div>

</body>
</html>
