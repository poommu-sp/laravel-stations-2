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

    th,
    td {
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
                        <td>
                            <!-- check if current seat->id is exist in array of reservedSeats -->
                            @php
                                $isReserved = in_array($seat->id, $reservedSeats); 
                            @endphp
                            @if ($isReserved)
                                <div style="background-color: grey;">
                                    {{ $seat->row }}-{{ $seat->column }}
                                </div>
                            @else
                                <div>
                                    <a href="{{ route('create.reservation', ['movie_id' => $movie_id, 'schedule_id' => $schedule_id, 'date' => $date, 'sheetId' => $seat->id]) }}">
                                        {{ $seat->row }}-{{ $seat->column }}
                                    </a>
                                </div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>


</html>
