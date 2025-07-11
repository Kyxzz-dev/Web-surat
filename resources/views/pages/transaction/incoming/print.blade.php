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
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        font-size: 12px;
        color: #000;
        background: #fff;
        margin: 40px;
        padding: 0;
    }

    h1, h2, h4 {
        margin: 0;
        padding: 0;
    }

    h1 {
        font-size: 20px;
        font-weight: bold;
        color: #FFAB00;
    }

    h4 {
        font-size: 14px;
        margin-bottom: 5px;
        font-weight: normal;
    }

    hr {
        border: 1px solid #FFAB00;
        margin: 15px 0 20px;
    }

    #filter-section {
        margin: 20px 0;
        font-size: 13px;
        line-height: 1.4;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 12px;
    }

    th, td {
        border: 1px solid #000;
        padding: 8px 10px;
        vertical-align: top;
        text-align: left;
    }

    th {
        background-color: #FFAB00;
        color: #000;
        font-weight: bold;
    }

    tbody tr:nth-child(even) {
        background-color: #fdf4e0; /* versi terang dari oranye */
    }

    .header-container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    margin-bottom: 10px;
}

.brand-left {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.app-brand-text {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.brand-center {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
}


    @media print {
        body {
            margin: 0;
        }

        table, th, td {
            page-break-inside: avoid;
        }
    }
</style>


</head>
<body onload="window.print()">

    <div class="header-container">
    <div class="brand-left">
        <img src="{{ asset('logo-black.png') }}" alt="{{ config('app.name') }}" width="40">
        <div class="app-brand-text">
            <div class="fw-bold text-black" style="font-size: 18px;">Sinorat</div>
            <div class="text-muted" style="font-size: 12px;">Sistem informasi NOmor suRAT</div>
        </div>
    </div>
    
    <div class="brand-center">
        <h1>{{ $config['institution_name'] }}</h1>
        <h4>{{ $config['institution_address'] }}</h4>
    </div>
</div>
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
            
            <th>{{ __('model.letter.reference_number') }}</th>
            <th>{{ __('model.letter.letter_date') }}</th>
            <th>{{ __('model.letter.from') }}</th>
            <th>{{ __('model.letter.description') }}</th>
            <th>{{ __('model.letter.note') }}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($data as $letter)
            <tr>
                
                <td>{{ $letter->reference_number }}</td>
                <td>{{ $letter->formatted_letter_date }}</td>
                <td>{{ $letter->from }}</td>
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
