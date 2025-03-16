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

    <h1>Create Reservation</h1>

    <form action="{{ route('admin.store.reservation') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="movie_id">映画</label>
            <select name="movie_id" id="movie_id" class="form-control">
                <option value="" disabled selected>映画を選択</option>
                @foreach ($movies as $movie)
                    <option value="{{ $movie->id }}">{{ $movie->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="schedule_id">スケジュール</label>
            <select name="schedule_id" id="schedule_id" class="form-control">
                <option value="" disabled selected>スケジュールを選択</option>
                @foreach ($schedules as $schedule)
                    <option value="{{ $schedule->id }}">
                        {{ $schedule->start_time }} - {{ $schedule->end_time }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="sheet_id">席</label>
            <select name="sheet_id" id="sheet_id" class="form-control">
                <option value="" disabled selected>席を選択</option>
                @foreach ($sheets as $sheet)
                    <option value="{{ $sheet->id }}">{{ strtoupper($sheet->row) }} - {{ $sheet->column }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="date">予約日</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ $date }}"
                readonly>
        </div>

        <div class="form-group">
            <label for="name">名前 :</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">メール :</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">予約</button>
    </form>

</body>
