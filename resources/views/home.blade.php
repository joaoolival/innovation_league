<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Home Innovation League</title>
        <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/bootstrap/css/style.css">
    </head>
    <body class="antialiased">
        <div class="bgimage">
            
            <div class="row">

                <!-- GRUPO A -->
                <div class="column">
                    @php
                        //dd($leagues)
                        $pos=0;
                    @endphp
                    <div class="bgtitulogrupo">
                        Grupo A
                    </div>
                    <div class="bggrupo">
                        <table>
                            <tr style="border-bottom: none;">
                                <th style="text-align: left;">POS</th>
                                <th style="text-align: left;">EQUIPA</th>
                                <th>J</th>
                                <th>W</th>
                                <th>D</th>
                                <th>L</th>
                                <th>PTS</th>
                            </tr>
                            @foreach($leagues as $league)
                                @if ($league[0]->Group_name == 'A')
                                    @foreach($league as $team)
                                    @php
                                        //dd($team);
                                        $pos++;
                                    @endphp
                                        <tr>
                                        <td style="text-align: left">{{$pos}}</td>
                                            <td style="text-align: left">{{$team->Name}}</td>
                                            <td>{{$team->Matches}}</td>
                                            <td>{{$team->Won}}</td>
                                            <td>{{$team->Drawn}}</td>
                                            <td>{{$team->Lost}}</td>
                                            <td>{{$team->Points}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </table>
                    </div>
                </div>
                        <!-- FIM GRUPO A -->  
                <br>
    
                <!-- GRUPO B -->
                <div class="column">
                    @php
                        //dd($leagues)
                        $pos=0;
                    @endphp
                    <div class="bgtitulogrupo">
                        Grupo B
                    </div>
                    <div class="bggrupo">
                        <table>
                            <tr style="border-bottom: none;">
                                <th style="text-align: left;">POS</th>
                                <th style="text-align: left;">EQUIPA</th>
                                <th>J</th>
                                <th>W</th>
                                <th>D</th>
                                <th>L</th>
                                <th>PTS</th>
                            </tr>
                            @foreach($leagues as $league)
                                @if ($league[0]->Group_name == 'B')
                                    @foreach($league as $team)
                                    @php
                                        $pos++;
                                    @endphp
                                        <tr>
                                        <td style="text-align: left">{{$pos}}</td>
                                            <td style="text-align: left">{{$team->Name}}</td>
                                            <td>{{$team->Matches}}</td>
                                            <td>{{$team->Won}}</td>
                                            <td>{{$team->Drawn}}</td>
                                            <td>{{$team->Lost}}</td>
                                            <td>{{$team->Points}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </table>
                    </div>
                </div>
                        <!-- FIM GRUPO B -->
                <br>
                <!-- GRUPO C -->
                <div class="column">
                    @php
                        //dd($leagues)
                        $pos=0;
                    @endphp
                    <div class="bgtitulogrupo">
                        Grupo C
                    </div>
                    <div class="bggrupo">
                        <table>
                            <tr style="border-bottom: none;">
                                <th style="text-align: left;">POS</th>
                                <th style="text-align: left;">EQUIPA</th>
                                <th>J</th>
                                <th>W</th>
                                <th>D</th>
                                <th>L</th>
                                <th>PTS</th>
                            </tr>
                            @foreach($leagues as $league)
                                @if ($league[0]->Group_name == 'C')
                                    @foreach($league as $team)
                                    @php
                                        $pos++;
                                    @endphp
                                        <tr>
                                        <td style="text-align: left">{{$pos}}</td>
                                            <td style="text-align: left">{{$team->Name}}</td>
                                            <td>{{$team->Matches}}</td>
                                            <td>{{$team->Won}}</td>
                                            <td>{{$team->Drawn}}</td>
                                            <td>{{$team->Lost}}</td>
                                            <td>{{$team->Points}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </table>
                    </div>
                </div>
                        <!-- FIM GRUPO c     -->
            </div><!--row-->
            
        </div>
    </body>
</html>
