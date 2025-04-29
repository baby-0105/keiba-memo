<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>レース一覧</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        a { display: block; margin-bottom: 10px; text-decoration: none; color: #0366d6; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>レース一覧</h1>

    @foreach ($races as $race)
        <a href="{{ route('races.horsesByRace', $race->id) }}">
            {{ $race->name }}
        </a>
    @endforeach
</body>
</html>
