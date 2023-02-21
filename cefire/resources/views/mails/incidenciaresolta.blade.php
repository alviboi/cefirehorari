<!DOCTYPE html>
<html lang="es">

<head>
    <!--Required meta tags-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Incidència {{ $dat['estat'] }}</title>
</head>

<body>
    <h2><b>La incidència del dia {{ $dat['data'] }} ha pasat a estat de {{ $dat['estat'] }}.</b></h2>
    <h2><b>Assesor: </b>{{ $dat['nombre'] }}</h2>
    <h2><b>Incidència: </b>{{ $dat['incidencia'] }}</h2>
</body>

</html>
