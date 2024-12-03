@extends('layouts.app')

@section('header')
    Aufträge
@endsection
@section('main')
    <div class="container">
        <div id="results">
            <div id="addresses"></div>
            <div id="distance"></div>
        </div>
        
        <script>
        const evtSource = new EventSource("/orders/distance");
        
        evtSource.onmessage = function(event) {
            const data = JSON.parse(event.data);
            console.log("data", data, event)
            if (data.status === 'addresses') {
                document.getElementById('addresses').innerHTML = `
                    Von: ${data.data.address1}<br>
                    Nach: ${data.data.address2}
                `;
            } else if (data.status === 'result') {
                document.getElementById('distance').innerHTML = `
                    Distanz: ${data.data.distance} km
                `;
                evtSource.close();
            }
        };
        </script>
    </div>
@endsection