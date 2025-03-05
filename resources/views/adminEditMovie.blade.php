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

    <h1>映画編集</h1>
    <form method="post" action="{{ route('admin.update.movie', $movie->id) }}">
        @csrf
        @method('patch')
        <div>
            <label for="title">映画タイトル : </label>
            <input type="text" id="title" name="title" value="{{ old('title', $movie->title) }}" /><br>
            <label for="image_url">画像URL : </label>
            <input type="text" id="image_url" name="image_url"
                value="{{ old('image_url', $movie->image_url) }}" /><br>
            <label for="published_year">公開年 : </label>
            <input type="text" id="published_year" name="published_year"
                value="{{ old('published_year', $movie->published_year) }}" /><br>
            <label for="is_showing">公開中かどうか : </label>
            <input type="hidden" name="is_showing" value="0">
            <input type="checkbox" name="is_showing" value="1" @checked(old('is_showing', $movie->is_showing))>
            <label for="description">概要 : </label><br>
            <textarea id="description" name="description"> {{ old('description', $movie->description) }} </textarea><br>
            <label for="genre">ジャンル : </label>
            <input type="text" id="genre" name="genre" value="{{ old('genre', $movie->genre->name) }}"/><br>
            <button type="submit">更新</button>
        </div>
    </form>
    <a href="{{ url()->previous() }}">戻る</a>
</body>
