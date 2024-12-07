@extends('layouts.app')

@section('main')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6">Routenberechnung</h1>

        @if(!$success)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ $error }}
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border-r pr-6">
                    <h2 class="text-lg font-semibold mb-2">Startadresse:</h2>
                    <p class="text-gray-700">
                        {{ $address1['street'] }}<br>
                        {{ $address1['zip_code'] }} {{ $address1['city'] }}
                    </p>
                </div>
                
                <div class="pl-6">
                    <h2 class="text-lg font-semibold mb-2">Zieladresse:</h2>
                    <p class="text-gray-700">
                        {{ $address2['street'] }}<br>
                        {{ $address2['zip_code'] }} {{ $address2['city'] }}
                    </p>
                </div>
            </div>

            <div class="mt-8 bg-gray-50 rounded p-4">
                <h2 class="text-lg font-semibold mb-4">Routeninformationen:</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-gray-600">Entfernung:</span>
                        <span class="font-semibold ml-2">{{ $distance }} km</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Fahrzeit:</span>
                        <span class="font-semibold ml-2">{{ $duration }} Minuten</span>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('orders.test') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    Neue Berechnung
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
