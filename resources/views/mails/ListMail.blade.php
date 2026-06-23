<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body>
    <h3>Witaj {{ $user }}</h3>
    <p> Oto Twoja list składników na <b>{{ $meal }}</b> </p>
    <p> Składniki: </p>
    @foreach ($list as $product)
        <p>
            <span> - {{ $product['ingredient'] }} - {{ $product['count'] }} {{ $product['metric'] }} </span>
        </p>
    @endforeach
    {{-- <p>{{ $list[0]->$ingredient }}</p> --}}
    <br>
    <p>Miłych przygód kulinarnych</p>
    <p>Well4Work</p>
</body>
</html>