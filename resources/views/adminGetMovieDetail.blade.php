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
        <div>{{ session('success') }}</div> <br>
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
    <h1>映画詳細</h1>
    <div>
        <label for="title">映画タイトル : </label>
        <p>{{ $movie->title }}</p>
    </div>
    <div>
        <label for="image_url">画像URL : </label>
        <p>{{ $movie->image_url }}</p>
    </div>
    <div>
        <label for="published_year">公開年 : </label>
        <p>{{ $movie->published_year }}</p>
    </div>
    <div>
        <label for="is_showing">公開中かどうか : </label>
        <p>{{ $movie->is_showing ? '上映中' : '上映予定' }}</p>
    </div>
    <div>
        <label for="description">概要 : </label><br>
        <p>{{ $movie->description }}</p>
    </div>
    <div>
        <label for="genre">ジャンル : </label>
        <p>{{ $movie->genre->name }}</p>
    </div>
    <div>
        <label for="schedule">スケジュール一覧 : </label>
        @foreach ($movie->schedules as $schedule)
            <p>開始時刻: {{ $schedule->start_time }}</p>
            <p>終了時刻: {{ $schedule->end_time }}</p>
            <br>
        @endforeach
    </div>
    <div>
    <a href="{{ route('admin.create.schedule', $movie->id) }}">
        <button>スケジュール作成</button>
    </a>
    </div>
    <div>
    <a href="{{ route('admin.list.movie') }}">
        <button>戻る</button>
    </a>
    </div>
</body>
