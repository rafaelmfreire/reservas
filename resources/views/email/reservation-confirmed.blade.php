<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body style="background-color: #f1f5f9; padding: 32px;">
    <div style="background-color: white; padding: 16px; border-radius: 8px; border: solid 1px #94a3b8">
        <h1>{{ $mailData['title'] }}</h1>
        <p>{{ $mailData['body'] }}</p>

        <h2>Detalhes da solicitação:</h2>
        <p>Descrição do evento: {{ $mailData['description'] }}</p>

        <ul>
            Datas:
            @foreach($mailData['dates'] as $date)
            <li>{{ \Carbon\Carbon::parse($date->start_at)->format('d/m/Y') }} das {{ \Carbon\Carbon::parse($date->start_at)->format('H:i')}} até {{ \Carbon\Carbon::parse($date->end_at)->format('H:i') }}</li>
            @endforeach
        </ul>
    </div>
</body>

</html>