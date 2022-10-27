<!DOCTYPE html>
<html lang="ca">
    <head>
        <!--Required meta tags-->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Vacances {{ $dat['estat'] }}</title>
    </head>
    <body>
        <h2><b>Assesor: </b>{{ $dat['nombre'] }}</h2>
        <h2><b>Les vacances del dia </b>{{ $dat['fecha'] }} ha passat a estat de {{ $dat['estat'] }}.</h2>
        @if ($dat['estat']=='Eliminada')
            <h2>A no ser que hages eliminat tú el dia, qualsevol aclariment que vullgues fer has de dirigir-te a l'adminsitrador.</h2>
        @else
            <h2>Una vegada Aprovat el dia, si finalment l'elimines cal que avises a l'adminsitrador'.</h2>
        @endif
        <br><br>
    <h2><a href="{{ $dat ['link'] }}">Afegir a Google Calendar</a></h2>
    </body>
</html>
