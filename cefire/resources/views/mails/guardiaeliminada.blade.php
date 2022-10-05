<!DOCTYPE html>
<html lang="es">
    <head>
        <!--Required meta tags-->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Guardia eliminada</title>
    </head>
    <body>
        <h1>Guardia eliminada</h1>
        <h2><b>Assesor: </b>{{ $dat['nombre'] }}</h2>
        <h2><b>Fecha: </b>{{ $dat['fecha'] }} per la {{ $dat['rato'] }}</h2>
        <br><br>
    <h2><a href="{{ $dat ['link'] }}">Afegir a Google Calendar</a></h2>
    </body>
</html>
