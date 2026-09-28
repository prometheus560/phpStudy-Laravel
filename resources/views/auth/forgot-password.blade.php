<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Forgot Password - Study Planner
    </title>

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
            padding: 30px;
        }

        .card {
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
            margin: 0 auto 20px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .card-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .card-header h1 {
            font-size: 22px;
            color: #f9fafb;
            margin-bottom: 5px;
        }

        .card-header p {
            color: #8b8fa3;
            font-size: 13px;
        }

        .card-content h2 {
            color: #f9fafb;
            font-size: 24px;
            text-align: center;
            margin-bottom: 6px;
        }

        .description {
            color: #8b8fa3;
            font-size: 13px;
            line-height: 1.5;
            text-align: center;
            margin-bottom: 26px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            background: rgba(34,197,94,0.12);
            color: #4ade80;
            border: 1px solid rgba(34,197,94,0.25);
            font-size: 13px;
        }

        .errors {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            background: rgba(239,68,68,0.12);
            color: #f87171;
            border: 1px solid rgba(239,68,68,0.25);
            font-size: 13px;
        }

        .errors ul {
            padding-left: 18px;
        }

        .errors li {
            margin: 3px 0;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
            font-size: 13px;
            color: #c7c9d9;
        }

        input {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #2a2a38;
            border-radius: 8px;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #f3f4f6;
            background: #1b1b28;
        }

        input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.18);
        }

        input::placeholder {
            color: #6b7080;
        }

        button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            font-family: Arial, sans-serif;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(37,99,235,0.35);
        }

        button:hover {
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

        @media (max-width: 500px) {

            body {
                padding: 20px;
            }

            .card {
                padding: 30px 22px;
            }

            .card-header h1 {
                font-size: 20px;
            }

        }

    </style>

</head>

<body>

<div class="card">

    <div class="card-header">

       <div class="logo">
       <img src="{{ asset('logo.png') }}" alt="Study Planner Logo">
      </div>

        <h1>
            Study Planner
        </h1>

        <p>
            Account Recovery
        </p>

    </div>

    <div class="card-content">

        <h2>
            Forgot Password?
        </h2>

        <p class="description">
            Enter your email address
        </p>

        @if (session('status'))

            <div class="alert">
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

        <form method="POST"
              action="{{ route('password.email') }}">

            @csrf

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

            <button type="submit">
               Reset
            </button>

        </form>

        <a href="{{ route('login') }}"
           class="back">

            Back to Login

        </a>

    </div>

</div>

</body>

</html>