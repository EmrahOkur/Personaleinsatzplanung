<x-app-layout>
    @section('header')
        
    @endsection

    @section('main')
        <div class="w-100 h-100 d-flex flex-column justify-content-center align-items-center">
            <img src="images/home.jpg" style="height: 400px; width: 500px;" />
            <h1>Willkommen, {{Auth::user()->getFullName();}}!</h1>
        </div>
    @endsection
</x-app-layout>