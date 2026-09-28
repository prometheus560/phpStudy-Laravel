@extends('layouts.app')

@section('content')
        
<style>

    html,
    body {
        margin: 0;
        padding: 0;
    }

    .dashboard-page {
        background-color: #0a0a12;
        min-height: calc(100vh - 74px);
        padding: 35px 40px 50px;
        box-sizing: border-box;
    }

    .dashboard-container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .dashboard-title {
        color: #f9fafb;
        font-family: Arial, sans-serif;
        font-size: 32px;
        font-weight: bold;
        margin: 0 0 6px 0;
    }

    .dashboard-subtitle {
        color: #8b8fa3;
        font-family: Arial, sans-serif;
        font-size: 16px;
        margin: 0 0 28px 0;
    }

    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }

    .dashboard-card {
        background-color: #14141f;
        border: 1px solid #23232f;
        border-radius: 10px;
        padding: 24px;
        min-height: 170px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.35);
        box-sizing: border-box;
    }

    .dashboard-card h2 {
        color: #f9fafb;
        font-family: Arial, sans-serif;
        font-size: 21px;
        font-weight: bold;
        margin: 0 0 20px 0;
    }

    .dashboard-number {
        color: #60a5fa;
        font-family: Arial, sans-serif;
        font-size: 38px;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .dashboard-description {
        color: #8b8fa3;
        font-family: Arial, sans-serif;
        font-size: 15px;
        margin: 0 0 16px 0;
    }
 .dashboard-button {
        display: inline-block;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        text-decoration: none;
        padding: 10px 17px;
        border-radius: 7px;
        font-family: Arial, sans-serif;
        font-size: 15px;
        font-weight: bold;
        border: none;
        box-shadow: 0 6px 16px rgba(37,99,235,0.3);
    }

    .dashboard-button:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
    }

    .subjects-card {
        background-color: #14141f;
        border: 1px solid #23232f;
        border-radius: 10px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.35);
        overflow: hidden;
        margin-bottom: 28px;
    }

    .subjects-header {
        padding: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        box-sizing: border-box;
    }

    .subjects-header h2 {
        color: #f9fafb;
        font-family: Arial, sans-serif;
        font-size: 26px;
        font-weight: bold;
        margin: 0 0 6px 0;
    }

    .subjects-header p {
        color: #8b8fa3;
        font-family: Arial, sans-serif;
        font-size: 15px;
        margin: 0;
    }

    .subjects-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .subjects-table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
    }

    .subjects-table thead {
        background-color: #1b1b28;
    }

    .subjects-table th {
        color: #c7c9d9;
        font-size: 15px;
        font-weight: bold;
        text-align: left;
        padding: 15px;
        border-bottom: 1px solid #23232f;
    }

    .subjects-table td {
        color: #e5e7eb;
        font-size: 15px;
        padding: 15px;
        border-bottom: 1px solid #23232f;
    }

    .subjects-table tbody tr:hover {
        background-color: #1b1b28;
    }

    .subjects-table tbody tr:last-child td {
        border-bottom: none;
    }


    /* =========================
       EMPTY SUBJECT MESSAGE
    ========================= */

    .empty-message {
        color: #8b8fa3;
        text-align: center;
        padding: 38px;
        font-family: Arial, sans-serif;
        font-size: 16px;
    }

    .empty-message strong {
        color: #e5e7eb;
    }


    /* =========================
       RESPONSIVE
    ========================= */

    @media (max-width: 1000px) {

        .dashboard-cards {
            grid-template-columns: 1fr;
        }

        .dashboard-container {
            max-width: 900px;
        }

    }


    @media (max-width: 700px) {

        .dashboard-page {
            padding: 25px 20px 40px;
        }

        .dashboard-title {
            font-size: 28px;
        }

        .dashboard-subtitle {
            font-size: 15px;
        }

        .subjects-header {
            flex-direction: column;
            align-items: flex-start;
        }

    }


    @media (max-width: 500px) {

        .dashboard-page {
            padding: 20px 15px 35px;
        }

        .dashboard-card {
            padding: 20px;
        }

        .subjects-header {
            padding: 20px;
        }

    }

</style>


<div class="dashboard-page">

    <div class="dashboard-container">

        <h1 class="dashboard-title">
            Welcome back, {{ $user->name }}
        </h1>

        <p class="dashboard-subtitle">
            Here's an overview of your study activities.
        </p>

        <div class="dashboard-cards">


            <!-- TOTAL SUBJECTS -->

            <div class="dashboard-card">

                <h2>
                    Total Subjects
                </h2>

                <div class="dashboard-number">
                    {{ $subjects->count() }}
                </div>

                <p class="dashboard-description">
                    Your subjects
                </p>

            </div>


            <!-- TOTAL TASKS -->

            <div class="dashboard-card">

                <h2>
                    Total Tasks
                </h2>

                <div class="dashboard-number">
                    {{ $subjects->sum('tasks_count') }}
                </div>

                <p class="dashboard-description">
                    All your tasks
                </p>

            </div>


            <!-- GET STARTED -->

            <div class="dashboard-card">

                <h2>
                    Get Started
                </h2>

                <p class="dashboard-description">
                    Manage your study tasks.
                </p>

                <a
                    href="{{ route('tasks.index') }}"
                    class="dashboard-button"
                >
                    View Tasks
                </a>

            </div>


        </div>

        <div class="subjects-card">


            <div class="subjects-header">

                <div>

                    <h2>
                        Your Subjects
                    </h2>

                    <p>
                        Keep track of your subjects and their tasks.
                    </p>

                </div>


                <a
                    href="{{ route('subjects.index') }}"
                    class="dashboard-button"
                >
                    Manage Subjects
                </a>

            </div>


            @if($subjects->count() > 0)


                <div class="subjects-table-wrapper">

                    <table class="subjects-table">

                        <thead>

                            <tr>

                                <th>
                                    Subject
                                </th>

                                <th>
                                    Tasks
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($subjects as $subject)

                                <tr>

                                    <td>
                                        {{ $subject->subject_name }}
                                    </td>

                                    <td>
                                        {{ $subject->tasks_count }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


            @else


                <div class="empty-message">

                    <strong>
                        No subjects yet
                    </strong>

                    <br>

                    Start by adding your first subject.

                    <br>
                    <br>

                    <a
                        href="{{ route('subjects.index') }}"
                        class="dashboard-button"
                    >
                        Add Subject
                    </a>

                </div>


            @endif


        </div>


    </div>

</div>

@endsection