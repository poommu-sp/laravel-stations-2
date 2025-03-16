<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Practice</title>
</head>

<body>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div> <br>
    @endif
    @if (session('errors'))
        <div>
            <ul>
                @foreach (session('errors')->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('store.reservation') }}" method="POST">
        @csrf

        <!-- hidden param -->

        <input type="hidden" name="movie_id" value="{{ $movie_id }}">
        <input type="hidden" name="schedule_id" value="{{ $schedule_id }}">
        <input type="hidden" name="sheet_id" value="{{ $sheet_id }}">
        <input type="hidden" name="date" value="{{ $date }}">

        <!-- Email & name input with validation -->

        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
        <input type="hidden" name="name" value="{{ auth()->user()->name }}">
        <input type="hidden" name="email" value="{{ auth()->user()->email }}">

        <button type="submit">予約</button>
    </form>
</body>
