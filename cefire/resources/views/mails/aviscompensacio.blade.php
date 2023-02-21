<!DOCTYPE html>
<html lang="es">

<head>
    <!--Required meta tags-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Compensació {{ $dat['estat'] }}</title>
</head>

<body>
    <h2><b>Assesor: </b>{{ $dat['nombre'] }}</h2>
    <h2><b>La compensació del dia </b>{{ $dat['fecha'] }} en el periode de {{ $dat['rato'] }} ha passat a estat de
        {{ $dat['estat'] }}.</h2>
    @if ($dat['estat'] == 'Eliminada')
        <h2>A no ser que hages eliminat tu la compensació, qualsevol aclariment que vullgues fer has de dirigir-te a
            l'administrador.</h2>
    @else
        <h2>Una vegada Aprovada la compensació, si finalment l'elimines cal que avises a l'Administrador.</h2>
    @endif
    <br><br>
    <h2><a href="{{ $dat['link'] }}">Afegir a Google Calendar</a></h2>
</body>

</html>
