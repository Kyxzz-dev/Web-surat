<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        h1 {
            margin-bottom: 5px;
        }

        h4 {
            margin-top: 0;
            font-weight: normal;
        }

        hr {
            margin: 10px 0 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }

        th, td {
            border: 1px solid black;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        #filter-section {
            margin: 20px 0;
            text-align: left;
        }
    </style>
</head>
<body onload="window.print()">

    <h1>{{ $config['institution_name'] }}</h1>
    <h4>{{ $config['institution_address'] }}</h4>
    <hr>

    <h2>{{ $title }}</h2>

    @if($since && $until && $filter)
        <div id="filter-section">
            <strong>{{ __('model.letter.' . $filter) }}:</strong> {{ "$since - $until" }}<br>
            <strong>Total:</strong> {{ count($data) }}
        </div>
    @endif

    <table>
        <thead>
        <tr>
            <th>{{ __('model.letter.agenda_number') }}</th>
            <th>{{ __('model.letter.reference_number') }}</th>
            <th>{{ __('model.letter.from') }}</th>
            <th>{{ __('model.letter.letter_date') }}</th>
            <th>{{ __('model.letter.description') }}</th>
            <th>{{ __('model.letter.note') }}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($data as $letter)
            <tr>
                <td>{{ $letter->agenda_number }}</td>
                <td>{{ $letter->reference_number }}</td>
                <td>{{ $letter->from }}</td>
                <td>{{ $letter->formatted_letter_date }}</td>
                <td>{{ $letter->description }}</td>
                <td>{{ $letter->note }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align: center;">{{ __('menu.general.empty') }}</td>
            </tr>
        @endforelse
        </tbody>
    </table>

</body>
</html>
