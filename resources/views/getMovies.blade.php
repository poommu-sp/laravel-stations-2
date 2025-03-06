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
    <div>
        <form action="{{ route('movie.search') }}" method="get">
            <div>
                <label for="keyword">タイトル</label>
                <input type="text" id="keyword" name="keyword" value="{{ $keyword ?? '' }}">
            </div>
            <div>
                <label for="all">すべて</label>
                <input type="radio" id="all" name="is_showing" value=""
                    {{ $is_showing === null ? 'checked' : '' }}>
            </div>
            <div>
                <label for="showing">公開中</label>
                <input type="radio" id="showing" name="is_showing" value="1"
                    {{ $is_showing === '1' ? 'checked' : '' }}>
            </div>
            <div>
                <label for="scheduled">公開予定</label>
                <input type="radio" id="scheduled" name="is_showing" value="0"
                    {{ $is_showing === '0' ? 'checked' : '' }}>
            </div>
            <button type="submit">検索</button>
        </form>
    </div>
    <br>
    <table>
        <thead>
            <tr>
                <th>タイトル</th>
                <th>画像</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($movies as $movie)
                <tr>
                    <td>
                        <a href="{{ route('movie.detail', $movie->id) }}">
                            {{ $movie->title }}
                        </a>
                    </td>
                    <td> <img src={{ $movie->image_url }}> </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div>
        {{ $movies->links() }}
    </div>
</body>

</html>
