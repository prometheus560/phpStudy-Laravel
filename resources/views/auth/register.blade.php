<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Create Account - Study Planner</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #0a0a12;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.04) 1px, transparent 1px),
                radial-gradient(circle at 70% 60%, rgba(255,255,255,0.03) 1px, transparent 1px),
                radial-gradient(circle at 40% 80%, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 140px 140px, 180px 180px, 160px 160px;
            color: #e5e7eb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .register-wrapper {
            width: 100%;
            max-width: 1050px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .branding {
            padding: 20px;
        }

        .logo {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 22px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .branding h1 {
            font-size: 48px;
            color: #f9fafb;
            margin-bottom: 15px;
        }

        .branding p {
            color: #8b8fa3;
            font-size: 20px;
            line-height: 1.5;
            max-width: 500px;
        }

        .features {
            margin-top: 30px;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            color: #c7c9d9;
        }

        .feature-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(59,130,246,0.12);
            border: 1px solid rgba(59,130,246,0.35);
            color: #60a5fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .register-card {
            background: #14141f;
            border: 1px solid #23232f;
            border-radius: 16px;
            padding: 36px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        .register-card h2 {
            text-align: center;
            color: #f9fafb;
            margin-bottom: 8px;
            font-size: 26px;
        }

        .register-subtitle {
            text-align: center;
            color: #8b8fa3;
            margin-bottom: 26px;
            font-size: 13px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 13px;
        }

        .alert-error {
            background: rgba(239,68,68,0.12);
            color: #f87171;
            border: 1px solid rgba(239,68,68,0.25);
        }

        .form-group {
            margin-bottom: 17px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            color: #c7c9d9;
            font-weight: bold;
            font-size: 13px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #2a2a38;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            outline: none;
            background: #1b1b28;
            color: #f3f4f6;
            transition: 0.2s;
        }

        .input-wrapper .form-control {
            padding-right: 45px;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.18);
        }

        .form-control::placeholder {
            color: #6b7080;
        }

        .password-toggle {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #8b8fa3;
            cursor: pointer;
            font-size: 17px;
        }

        .password-toggle:hover {
            color: #60a5fa;
        }

        .password-strength {
            margin-top: 8px;
            font-size: 13px;
            color: #8b8fa3;
        }

        .strength-bar {
            width: 100%;
            height: 5px;
            background: #23232f;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 7px;
        }

        .strength-fill {
            height: 100%;
            width: 0;
            transition: 0.25s;
        }

        .create-button {
            width: 100%;
            border: none;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            padding: 13px;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 6px 16px rgba(37,99,235,0.35);
        }

        .create-button:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: #6b7080;
            font-size: 12px;
        }

        .divider::before,
        .divider::after {
            content: "";
            height: 1px;
            background: #23232f;
            flex: 1;
        }

        .login-button {
            display: block;
            width: 100%;
            text-align: center;
            text-decoration: none;
            background: #1b1b28;
            border: 1px solid #2a2a38;
            color: #c7c9d9;
            padding: 11px;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: bold;
            transition: 0.2s;
        }

        .login-button:hover {
            background: #22222f;
            border-color: #3b82f6;
            color: #93c5fd;
        }

        .footer-text {
            text-align: center;
            color: #6b7080;
            font-size: 12px;
            margin-top: 22px;
        }

        @media (max-width: 800px) {

            body {
                padding: 20px;
            }

            .register-wrapper {
                grid-template-columns: 1fr;
                max-width: 460px;
                gap: 20px;
            }

            .branding {
                text-align: center;
                padding: 10px;
            }

            .logo {
                margin-left: auto;
                margin-right: auto;
            }

            .branding h1 {
                font-size: 38px;
            }

            .branding p {
                font-size: 17px;
                margin: auto;
            }

            .features {
                display: none;
            }

        }

        @media (max-width: 450px) {

            body {
                padding: 15px;
            }

            .register-card {
                padding: 26px 20px;
            }

            .branding h1 {
                font-size: 32px;
            }

        }

    </style>

</head>

<body>

<div class="register-wrapper">

    <div class="branding">

      <div class="logo">
    <img src="{{ asset('logo.png') }}" alt="Study Planner Logo">
      </div>

        <h1>
            Study Planner
        </h1>

        <p>
            Create your account and start organizing
            your subjects, tasks, and study schedule.
        </p>

        <div class="features">

            <div class="feature">

                <div class="feature-icon">
                    ✓
                </div>

                <span>
                    Create and manage your subjects
                </span>

            </div>

            <div class="feature">

                <div class="feature-icon">
                    ✓
                </div>

                <span>
                    Track your assignments and deadlines
                </span>

            </div>

            <div class="feature">

                <div class="feature-icon">
                    ✓
                </div>

                <span>
                    Build your personal study schedule
                </span>

            </div>

        </div>

    </div>


    <div class="register-card">

        <h2>
            Create New Account
        </h2>

        <p class="register-subtitle">
            It's quick and easy to get started.
        </p>


        @if ($errors->any())

            <div class="alert alert-error">

                <ul style="padding-left: 18px; margin: 0;">

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form method="POST"
              action="{{ url('/register') }}">

            @csrf


            <div class="form-group">

                <label for="name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    value="{{ old('name') }}"
                    placeholder="Enter your full name"
                    maxlength="100"
                    required
                    autofocus
                >

            </div>


            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    value="{{ old('email') }}"
                    placeholder="Enter your email"
                    maxlength="100"
                    required
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
                        class="form-control"
                        placeholder="Create a password"
                        required
                        oninput="checkPasswordStrength()"
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        onclick="togglePassword('password', 'passwordToggle')"
                        id="passwordToggle"
                    >
                        👁
                    </button>

                </div>

                <div class="password-strength">

                    <span id="strengthText">
                        Use at least 8 characters.
                    </span>

                    <div class="strength-bar">

                        <div
                            class="strength-fill"
                            id="strengthFill"
                        ></div>

                    </div>

                </div>

            </div>


            <div class="form-group">

                <label for="password_confirmation">
                    Confirm Password
                </label>

                <div class="input-wrapper">

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-control"
                        placeholder="Confirm your password"
                        required
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        onclick="togglePassword('password_confirmation', 'confirmToggle')"
                        id="confirmToggle"
                    >
                        👁
                    </button>

                </div>

            </div>


            <button
                type="submit"
                class="create-button"
            >
                Create Account
            </button>

        </form>


        <div class="divider">
            OR
        </div>


        <a
            href="{{ route('login') }}"
            class="login-button"
        >
            Already Have an Account? Log In
        </a>


        <p class="footer-text">
            Study Planner · Stay organized · Study smarter
        </p>

    </div>

</div>


<script>

function togglePassword(inputId, buttonId) {

    const password =
        document.getElementById(inputId);

    const button =
        document.getElementById(buttonId);

    if (password.type === "password") {

        password.type = "text";
        button.textContent = "🙈";

    } else {

        password.type = "password";
        button.textContent = "👁";

    }

}


function checkPasswordStrength() {

    const password =
        document.getElementById("password").value;

    const strengthText =
        document.getElementById("strengthText");

    const strengthFill =
        document.getElementById("strengthFill");

    let score = 0;

    if (password.length >= 8) {
        score++;
    }

    if (/[A-Z]/.test(password)) {
        score++;
    }

    if (/[a-z]/.test(password)) {
        score++;
    }

    if (/[0-9]/.test(password)) {
        score++;
    }

    if (/[^A-Za-z0-9]/.test(password)) {
        score++;
    }


    if (password.length === 0) {

        strengthText.textContent =
            "Use at least 8 characters.";

        strengthFill.style.width =
            "0";

        strengthFill.style.background =
            "transparent";

    } else if (score <= 2) {

        strengthText.textContent =
            "Weak password";

        strengthFill.style.width =
            "30%";

        strengthFill.style.background =
            "#ef4444";

    } else if (score <= 4) {

        strengthText.textContent =
            "Medium password";

        strengthFill.style.width =
            "65%";

        strengthFill.style.background =
            "#f59e0b";

    } else {

        strengthText.textContent =
            "Strong password";

        strengthFill.style.width =
            "100%";

        strengthFill.style.background =
            "#3b82f6";

    }

}

</script>

</body>

</html>