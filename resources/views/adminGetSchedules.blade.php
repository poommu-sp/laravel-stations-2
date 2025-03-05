<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Practice</title>
</head>

<script>
    function confirmDelete(event) {
        event.preventDefault();
        const result = window.confirm("これを削除してもよろしいですか？");
        if (result) {
            event.target.submit();
        }
    }
</script>

<body>
    <h1>スケジュール一覧</h1>
    @foreach ($movies as $movie)
        <h2>
            <a href="{{ route('admin.get.movie', $movie->id) }}">
                {{ $movie->title }} (ID: {{ $movie->id }})
            </a>
        </h2>
        <ul>
            @foreach ($movie->schedules as $schedule)
                <li>
                    開始時刻: {{ $schedule->start_time->format('Y-m-d H:i') }}  <br>
                    終了時刻: {{ $schedule->end_time->format('Y-m-d H:i') }} <br>
                    <a href="{{ route('admin.get.schedule', $schedule->id) }}">詳細</a> |
                    <a href="{{ route('admin.edit.schedule', $schedule->id) }}">編集</a> |
                    <form action="{{ route('admin.delete.schedule', $schedule->id) }}" method="POST"
                        onsubmit="confirmDelete(event)">
                        @csrf
                        @method('DELETE')
                        <button>削除</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endforeach
    <a href="{{ url()->previous() }}">戻る</a>
</body>
