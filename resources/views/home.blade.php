<x-app-layout>
    @section('header')
        
    @endsection

    @section('main')
        <div class="w-100 h-100 d-flex justify-content-center align-items-center">
            <h1>Willkommen, {{Auth::user()->getFullName();}}!</h1>
        </div>
    @endsection
</x-app-layout>