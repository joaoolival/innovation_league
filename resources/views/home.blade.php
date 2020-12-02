<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Home Innovation League</title>
        <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    </head>
    <body class="antialiased">
        @php
        
        if(isset($round_16[0])) 
        {
            dd('nao');
        }
        else {
            dd('sim');
        }
        
        //@foreach ($leagues as $league)
            
        //@endforeach
        @endphp
    </body>
</html>
