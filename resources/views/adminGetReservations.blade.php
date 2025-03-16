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
    <a href="{{ route('admin.create.reservation') }}">
        <button>予約を作成</button>
    </a>
    <table>
        <thead>
            <tr>
                <th>映画</th>
                <th>スケジュール</th>
                <th>シート</th>
                <th>名前</th>
                <th>メール</th>
                <th>状態</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->schedule->movie->title }}</td>
                    <td>{{ $reservation->schedule->start_time }} - {{ $reservation->schedule->end_time }}</td>
                    <td>{{ strtoupper($reservation->sheet->row . $reservation->sheet->column) }}</td>
                    <td>{{ $reservation->name }}</td>
                    <td>{{ $reservation->email }}</td>
                    <td>{{ $reservation->is_canceled ? 'キャンセル' : 'アクティブ' }}</td>
                    <td>
                        <a href="{{ route('admin.edit.reservation', $reservation->id) }}">
                            <button>編集</button>
                        </a>
                        <form method="post" action="{{ route('admin.delete.reservation', $reservation->id) }}" onsubmit="confirmDelete(event)">
                            @csrf
                            @method('delete')
                            <button>削除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


</body>

</html>
