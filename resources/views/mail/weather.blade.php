<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Weather</title>
</head>
<body>
    Dự báo thời tiết: <strong>{{ $mailData['location'] }} ({{ $mailData['country'] }})</strong>
    <p>Temperature: {{ $mailData['temp'] }}°C</p>
    <p>Wind: {{ $mailData['wind'] }} M/S</p>
    <p>Humidity: {{ $mailData['humidity'] }}%</p>
</body>
</html>