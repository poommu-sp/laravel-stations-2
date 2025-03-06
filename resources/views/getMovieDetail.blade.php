<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Practice</title>
</head>


<style>
    table {
          width: 100%;
          border-collapse: collapse;
      }
      th, td {
          padding: 10px;
          text-align: center;
          vertical-align: middle;
          border-bottom: 2px solid #ddd;
      }
      th {
          border-left: none;
          border-right: none;
      }
</style>

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
    <div class="movie-detail">
        <h1>タイトル : {{ $movie->title }}</h1>
        <img src={{ $movie->image_url }}>
        <p>公開年 : {{ $movie->published_year }}</p>
        <p>概要 : {{ $movie->description }} </p>
        @if ($movie->is_showing > 0)
            <p>公開中かどうか : 上映中</p>
        @else
            <p>公開中かどうか : 上映予定</p>
        @endif
        <p>ジャンル : {{ $movie->genre ? $movie->genre->name : '' }} </p>
        <h2>上映スケジュール</h2>
        <table>
            <thead>
                <tr>
                    <th>上映開始時刻</th>
                    <th>上映終了時刻</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($movie->schedules->sortBy('start_time') as $schedule)
                    <tr>
                        <td>{{ $schedule->start_time->format('H:i') }}</td>
                        <td>{{ $schedule->end_time->format('H:i') }}</td>
                        <td>
                            <a href="{{ route('sheet.reservation', ['movie_id' => $movie->id, 'schedule_id' => $schedule->id, 'date' => $schedule->start_time->format('Y-m-d')]) }}">
                                <button>座席を予約する</button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </div>
    <a href="{{ url()->previous() }}">戻る</a>
</body>

</html>
