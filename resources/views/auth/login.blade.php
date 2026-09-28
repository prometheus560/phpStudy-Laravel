<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - Study Planner</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #0a0a12;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.04) 1px, transparent 1px),
                radial-gradient(circle at 70% 60%, rgba(255,255,255,0.03) 1px, transparent 1px),
                radial-gradient(circle at 40% 80%, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 140px 140px, 180px 180px, 160px 160px;
            color: #e5e7eb;
            min-height: 100vh;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            background: #14141f;
            border: 1px solid #23232f;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            padding: 40px 36px;
        }

        .logo {
            width: 56px;
            height: 56px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            overflow: hidden;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand h1 {
            font-size: 22px;
            color: #f9fafb;
            margin-bottom: 5px;
        }

        .brand p {
            color: #8b8fa3;
            font-size: 13px;
        }

        .login-section h2 {
            color: #f9fafb;
            font-size: 24px;
            text-align: center;
            margin-bottom: 6px;
        }

        .subtitle {
            color: #8b8fa3;
            font-size: 13px;
            text-align: center;
            margin-bottom: 26px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .alert-success {
            background: rgba(34,197,94,0.12);
            color: #4ade80;
            border: 1px solid rgba(34,197,94,0.25);
        }

        .errors {
            background: rgba(239,68,68,0.12);
            color: #f87171;
            border: 1px solid rgba(239,68,68,0.25);
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .errors ul {
            padding-left: 18px;
        }

        .errors li {
            margin: 3px 0;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            color: #c7c9d9;
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 7px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="text"] {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #2a2a38;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            background: #1b1b28;
            color: #f3f4f6;
        }

        .form-group input::placeholder {
            color: #6b7080;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.18);
        }

        .password-input {
            padding-right: 60px !important;
        }

        .show-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #60a5fa;
            cursor: pointer;
            font-weight: bold;
            font-size: 13px;
        }

        .show-password:hover {
            color: #93c5fd;
        }

        .options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            font-size: 13px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #8b8fa3;
        }

        .remember input {
            width: auto;
            accent-color: #3b82f6;
        }

        .forgot-link {
            color: #60a5fa;
            text-decoration: none;
            font-weight: bold;
        }

        .forgot-link:hover {
            color: #93c5fd;
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            font-family: Arial, sans-serif;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(37,99,235,0.35);
        }

        .login-button:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0;
            color: #6b7080;
            font-size: 12px;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #23232f;
        }

        .register-button {
            display: block;
            width: 100%;
            padding: 11px;
            text-align: center;
            border: 1px solid #2a2a38;
            border-radius: 8px;
            color: #c7c9d9;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            background: #1b1b28;
        }

        .register-button:hover {
            background: #22222f;
            border-color: #3b82f6;
            color: #93c5fd;
        }

        .footer {
            text-align: center;
            margin-top: 22px;
            color: #6b7080;
            font-size: 12px;
        }

        @media (max-width: 500px) {

            .page {
                padding: 20px;
            }

            .login-container {
                padding: 30px 22px;
            }

            .brand h1 {
                font-size: 20px;
            }

        }

    </style>

</head>

<body>

<div class="page">

    <div class="login-container">

        <div class="brand">

            <div class="logo">
               <img src="{{ asset('logo.png') }}" alt="Study Planner Logo">
            </div>

            <h1>Study Planner</h1>
            <p>Organize your studies and stay on track.</p>

        </div>

        <div class="login-section">

            <h2>
                Welcome Back
            </h2>

            <p class="subtitle">
                Log in to continue to your Study Planner.
            </p>

            @if (session('status'))

                <div class="alert alert-success">
                    {{ session('status') }}
                </div>

            @endif

            @if ($errors->any())

                <div class="errors">

                    <ul>

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif

            <form method="POST" action="{{ url('/login') }}">

                @csrf

                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Enter your email"
                        required
                        autofocus
                    >

                </div>

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <div class="input-wrapper">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="password-input"
                            placeholder="Enter your password"
                            required
                        >

                        <button
                            type="button"
                            class="show-password"
                            onclick="togglePassword()"
                            id="showPasswordButton"
                        >
                            Show
                        </button>

                    </div>

                </div>

                <div class="options">

                    <label class="remember">

                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                        >

                        Remember me

                    </label>

                    <a
                        href="{{ route('password.request') }}"
                        class="forgot-link"
                    >
                        Forgot password?
                    </a>

                </div>

                <button
                    type="submit"
                    class="login-button"
                >
                    Log In
                </button>

            </form>

            <div class="divider">
                OR
            </div>

            <a
                href="{{ route('register') }}"
                class="register-button"
            >
                Create New Account
            </a>

            <div class="footer">
                Study Planner · Stay organized · Study smarter
            </div>

        </div>

    </div>

</div>

<script>

    function togglePassword() {

        const password =
            document.getElementById('password');

        const button =
            document.getElementById('showPasswordButton');

        if (password.type === 'password') {

            password.type = 'text';
            button.textContent = 'Hide';

        } else {

            password.type = 'password';
            button.textContent = 'Show';

        }

    }

</script>

</body>

</html>