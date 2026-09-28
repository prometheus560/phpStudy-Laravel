<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Study Planner</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #0a0a12;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.04) 1px, transparent 1px),
                radial-gradient(circle at 70% 60%, rgba(255,255,255,0.03) 1px, transparent 1px),
                radial-gradient(circle at 40% 80%, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 140px 140px, 180px 180px, 160px 160px;
            font-family: Arial, sans-serif;
            color: #e5e7eb;
            padding: 20px;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: #14141f;
            border: 1px solid #23232f;
            padding: 40px 36px;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        .logo {
            width: 56px;
            height: 56px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
            box-shadow: 0 8px 20px rgba(37,99,235,0.35);
        }

        h1 {
            text-align: center;
            margin-bottom: 10px;
            color: #f9fafb;
            font-size: 24px;
        }

        .description {
            text-align: center;
            color: #8b8fa3;
            font-size: 13px;
            margin-bottom: 28px;
        }

        .errors {
            padding: 12px 15px;
            background: rgba(239,68,68,0.12);
            color: #f87171;
            border: 1px solid rgba(244, 66, 66, 0.25);
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .errors ul {
            padding-left: 20px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
            font-size: 13px;
            color: #c7c9d9;
        }

        .field {
            position: relative;
            margin-bottom: 18px;
        }

        input {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #2a2a38;
            border-radius: 8px;
            font-size: 14px;
            font-family: Arial, sans-serif;
            background: #1b1b28;
            color: #f3f4f6;
        }

        input::placeholder {
            color: #6b7080;
        }

        input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.18);
        }

        .password {
            padding-right: 65px;
        }

        .show {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #60a5fa;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
        }

        .show:hover {
            color: #93c5fd;
        }

        .submit {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(37,99,235,0.35);
        }

        .submit:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 22px;
            color: #60a5fa;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .back:hover {
            color: #93c5fd;
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="card">

    <div class="logo">
        SP
    </div>

    <h1>Reset Password</h1>

    <p class="description">
        Create a new password for your Study Planner account.
    </p>

    @if ($errors->any())
        <div class="errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input
            type="hidden"
            name="token"
            value="{{ $token }}"
        >

        <input
            type="hidden"
            name="email"
            value="{{ $email }}"
        >

        <label for="password">
            New Password
        </label>

        <div class="field">

            <input
                type="password"
                id="password"
                name="password"
                class="password"
                placeholder="Enter new password"
                required
            >

            <button
                type="button"
                class="show"
                onclick="togglePassword('password', this)"
            >
                Show
            </button>

        </div>

        <label for="password_confirmation">
            Confirm Password
        </label>

        <div class="field">

            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="password"
                placeholder="Confirm new password"
                required
            >

            <button
                type="button"
                class="show"
                onclick="togglePassword('password_confirmation', this)"
            >
                Show
            </button>

        </div>

        <button type="submit" class="submit">
            Change Password
        </button>

    </form>

    <a href="{{ route('login') }}" class="back">
        Back to Login
    </a>

</div>

<script>
function togglePassword(id, button) {
    const input = document.getElementById(id);

    if (input.type === 'password') {
        input.type = 'text';
        button.textContent = 'Hide';
    } else {
        input.type = 'password';
        button.textContent = 'Show';
    }
}
</script>

</body>

</html>