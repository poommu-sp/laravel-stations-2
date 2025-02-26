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
    <table>
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th>スクリーン</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sheets as $row => $seats)
                <tr>
                    @foreach ($seats as $seat)
                        <td>{{ $seat->row }}-{{ $seat->column }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>


</html>
