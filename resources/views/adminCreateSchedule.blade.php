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

    <h1>スケジュール編集</h1>
    <form method="post" action="{{ route('admin.store.schedule', $movie->id) }}">
        @csrf
        <div>
            <label for="start_time_date">開始日付 : </label>
            <input type="date" id="start_time_date" name="start_time_date">
            <label for="start_time_time">開始時間 : </label>
            <input type="time" id="start_time_time" name="start_time_time">
        </div>
        <div>
            <label for="end_time_date">終了日付 : </label>
            <input type="date" id="end_time_date" name="end_time_date">
            <label for="end_time_time">終了時間 : </label>
            <input type="time" id="end_time_time" name="end_time_time">
        </div>
        <div>
            <label for="screen_id">スクリーン</label>
            <select name="screen_id" id="screen_id">
                @foreach ($screens as $screen)
                    <option value="{{ $screen->id }}">{{ $screen->name }}</option>
                @endforeach
            </select>
        </div>
        <input type="hidden" name="movie_id" value="{{ $movie->id }}">
        <button type="submit">保存</button>
    </form>

    <a href="{{ url()->previous() }}">戻る</a>

</body>
