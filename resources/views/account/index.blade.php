@extends('layouts.app')

@section('title', 'Account')

@section('content')

<div class="w-full">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">Account</h1>
        <p class="text-gray-400 mt-1">Manage your account information and password.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Account Information --}}
        <div class="bg-[#14141f] rounded-lg border border-[#23232f] shadow-sm p-6">

            <h2 class="text-xl font-semibold text-white mb-4">Account Information</h2>

            <div class="flex flex-col">

                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 py-4 border-b border-[#23232f]">
                    <span class="text-gray-400 font-semibold text-sm">Name</span>
                    <span class="text-gray-200 break-words sm:text-right">{{ auth()->user()->name }}</span>
                </div>

                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 py-4 border-b border-[#23232f]">
                    <span class="text-gray-400 font-semibold text-sm">Email</span>
                    <span class="text-gray-200 break-words sm:text-right">{{ auth()->user()->email }}</span>
                </div>

                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 py-4">
                    <span class="text-gray-400 font-semibold text-sm">User ID</span>
                    <span class="text-gray-200 break-words sm:text-right">{{ auth()->user()->id }}</span>
                </div>

            </div>

        </div>

        {{-- Change Password --}}
        <div class="bg-[#14141f] rounded-lg border border-[#23232f] shadow-sm p-6">

            <h2 class="text-xl font-semibold text-white mb-2">Change Password</h2>

            <p class="text-gray-400 mb-6 leading-relaxed">
                Update your password to keep your account secure.
            </p>

            <form method="POST" action="{{ route('account.password.update') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="current_password" class="block text-sm font-semibold text-gray-300 mb-2">
                        Current Password
                    </label>
                    <div class="flex gap-2">
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            required
                            class="flex-1 min-w-0 rounded-md border border-[#2a2a38] bg-[#1b1b28] text-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                        >
                        <button
                            type="button"
                            onclick="togglePassword('current_password', this)"
                            class="w-[65px] shrink-0 rounded-md border border-[#2a2a38] bg-[#1b1b28] text-sm font-medium text-gray-300 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-colors"
                        >
                            Show
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">
                        New Password
                    </label>
                    <div class="flex gap-2">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            class="flex-1 min-w-0 rounded-md border border-[#2a2a38] bg-[#1b1b28] text-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                        >
                        <button
                            type="button"
                            onclick="togglePassword('password', this)"
                            class="w-[65px] shrink-0 rounded-md border border-[#2a2a38] bg-[#1b1b28] text-sm font-medium text-gray-300 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-colors"
                        >
                            Show
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-300 mb-2">
                        Confirm New Password
                    </label>
                    <div class="flex gap-2">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            class="flex-1 min-w-0 rounded-md border border-[#2a2a38] bg-[#1b1b28] text-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                        >
                        <button
                            type="button"
                            onclick="togglePassword('password_confirmation', this)"
                            class="w-[65px] shrink-0 rounded-md border border-[#2a2a38] bg-[#1b1b28] text-sm font-medium text-gray-300 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-colors"
                        >
                            Show
                        </button>
                    </div>
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-md bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-2 text-sm font-semibold text-white hover:from-blue-500 hover:to-blue-600 shadow-lg shadow-blue-900/30 transition-colors"
                >
                    Change Password
                </button>

            </form>

        </div>

    </div>

    {{-- Security --}}
    <div class="bg-[#14141f] rounded-lg border border-[#23232f] shadow-sm p-6 mt-6">

        <h2 class="text-xl font-semibold text-white mb-4">Security</h2>

        <p class="text-gray-400 leading-relaxed mb-2">
            Your password is stored securely using Laravel's password hashing system.
        </p>

        <p class="text-gray-400 leading-relaxed">
            If you forget your password, you can use the
            <a href="{{ route('password.request') }}" class="text-blue-400 font-semibold hover:text-blue-300">
                password reset
            </a>
            option from the login page.
        </p>

    </div>

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

@endsection