<x-app-layout>

    @section("header")
        <span class="ls-3 ps-3 fs-4">Benutzer anlegen</span>
    @endsection
    
    @section("main")
        <!-- resources/views/components/users-form.blade.php -->
        <form method="POST" action="{{ route('users.create') }}" class="needs-validation p-5" novalidate>
            @csrf
            <x-users-form-fields :edit="false"/>
        </form>
    @endsection  
    
</x-app-layout>