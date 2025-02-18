<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Practice</title>
</head>

<body>
    @if (session('errors'))
        <div>
            <ul>
                @foreach (session('errors')->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="post" action="{{ route('store') }}">
        @csrf
        <div>
            <label for="title">映画タイトル : </label>
            <input type="text" id="title" name="title" /><br>
            <label for="image_url">画像URL : </label>
            <input type="text" id="image_url" name="image_url" /><br>
            <label for="published_year">公開年 : </label>
            <input type="text" id="published_year" name="published_year" /><br>
            <label for="is_showing">公開中かどうか : </label>
            <input type="hidden" name="is_showing" value="0">
            <input type="checkbox" name="is_showing" value="1" @checked(old('is_showing', 0))><br>
            <label for="description">概要 : </label><br>
            <textarea id="description" name="description"></textarea><br>
            <label for="genre">ジャンル : </label>
            <input type="text" id="genre" name="genre" /><br>
            <button type="submit">Save</button>
        </div>
    </form>
</body>
