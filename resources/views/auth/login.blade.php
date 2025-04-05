
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Primary Meta Tags -->
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="title" content="{{ config('app.name') }}">
    <meta name="author" content="Themesberg">
    <meta name="description" content="{{ config('app.name') }}">
    <meta name="keywords" content="sistem, informasi, funsional, spj" />
    <link rel="canonical" href="{{ url('/') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="{{ config('app.name') }}">
    <meta property="og:description" content="{{ config('app.name') }}">
    <meta property="og:image" content="{{ url('/') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="{{ config('app.name') }}">
    <meta property="twitter:description" content="{{ config('app.name') }}">
    <meta property="twitter:image" content="{{ url('/') }}">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="120x120" href="{{ url('/') }}/assets/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('/') }}/assets/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('/') }}/assets/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="{{ url('/') }}/assets/img/favicon/site.webmanifest">
    <link rel="mask-icon" href="{{ url('/') }}/assets/img/favicon/safari-pinned-tab.svg" color="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <!-- Sweet Alert -->
    <link type="text/css" href="{{ url('/') }}/vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Notyf -->
    <link type="text/css" href="{{ url('/') }}/vendor/notyf/notyf.min.css" rel="stylesheet">

    <!-- Volt CSS -->
    <link type="text/css" href="{{ url('/') }}/css/volt.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <link type="text/css" href="{{ url('/') }}/css/login.css" rel="stylesheet">

    <!-- font poppins to all -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body>
    <main class="login-wrapper">
        
        <div class="login-card">
            <div class="card-header">
                <div class="login-brand">
                    <h1>{{ config('app.name') }}</h1>
                    <p>Welcome back! Please login to your account</p>
                </div>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <i class="fas fa-envelope input-icon"></i>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                name="email" value="{{ old('email') }}" required autocomplete="email" 
                                placeholder="name@example.com" autofocus>
                                
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="password" class="form-label mb-0">Password</label>
                            <a href="./forgot-password.html" class="btn-link">Forgot password?</a>
                        </div>
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                name="password" required autocomplete="current-password" placeholder="Enter your password">
                            <span class="toggle-password" onclick="togglePassword()">
                                <i class="fas fa-eye"></i>
                            </span>
                                
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i> Sign In
                        </button>
                    </div>
                </form>
                
                <div class="divider">or continue with</div>
                
                <div class="social-login">
                    <a href="#" class="social-btn google">
                        <i class="fab fa-google"></i>
                    </a>
                    <a href="#" class="social-btn facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-btn apple">
                        <i class="fab fa-apple"></i>
                    </a>
                </div>
            </div>
            
            <div class="card-footer">
                <span>Don't have an account? <a href="#" class="btn-link">Create account</a></span>
            </div>
        </div>
    </main>

    <!-- Core Scripts -->
    <script src="{{ url('/') }}/vendor/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="{{ url('/') }}/vendor/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <!-- Sweet Alerts 2 -->
    <script src="{{ url('/') }}/vendor/sweetalert2/dist/sweetalert2.all.min.js"></script>
    
    <!-- Notyf -->
    <script src="{{ url('/') }}/vendor/notyf/notyf.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Add animation to form fields on focus
        const formControls = document.querySelectorAll('.form-control');
        formControls.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    </script>
</body>
</html>