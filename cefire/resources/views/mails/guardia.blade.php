<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Guardia afegida</title>
    </head>
    <body>
        <h1>Guardia afegida</h1>
        <h2><b>Assessor: </b>{{ $dat['nombre'] }}</h2>
        <h2><b>Data: </b>{{ $dat['fecha'] }} {{ $dat['rato'] }}</h2>
        <br><br>
    <h2><a href="{{ $dat ['link'] }}">Afegir a Google Calendar</a></h2>
    </body>
</html>
