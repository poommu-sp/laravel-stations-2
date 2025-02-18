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
    <table>
        <thead>
            <tr>
                <th>タイトル</th>
                <th>画像</th>
                <th>公開年</th>
                <th>概要</th>
                <th>上映中かどうか</th>
                <th>ジャンル</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($movies as $movie)
                <tr>
                    <td> {{ $movie->title }} </td>
                    <td> <img src={{ $movie->image_url }}> </td>
                    <td> {{ $movie->published_year }} </td>
                    <td> {{ $movie->description }} </td>
                    @if ($movie->is_showing > 0)
                        <td>上映中</td>
                    @else
                        <td>上映予定</td>
                    @endif
                    <td> {{ $movie->genre ? $movie->genre->name : '' }} </td>
                    <td>
                        <a href="{{ route('edit', $movie->id) }}">
                            <button>Edit</button>
                        </a>
                    </td>
                    <td>
                        <form method="post" action="{{ route('delete', $movie->id) }}" onsubmit="confirmDelete(event)">
                            @csrf
                            @method('delete')
                            <button>Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
