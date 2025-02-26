<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Practice</title>
</head>

<body>
    <h1>スケジュール詳細</h1>
    <h2>{{ $schedule->movie->title }} (ID: {{ $schedule->movie->id }})</h2>
    <div>
        <label for="start_time">開始時刻 : </label>
        <p>{{ $schedule->start_time->format('Y-m-d H:i') }}</p>
    </div>
    <div>
        <label for="end_time">終了時刻 : </label>
        <p>{{  $schedule->end_time->format('Y-m-d H:i') }}</p>
    </div>
    <div>
        <a href="{{ url()->previous() }}">戻る</a>
    </div>
</body>
