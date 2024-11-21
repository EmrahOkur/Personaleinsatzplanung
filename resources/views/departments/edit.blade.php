<x-app-layout>

    @section("header")
        <span class="ls-3 ps-3 fs-4">Abteilung ändern</span>
    @endsection
    
    @section("main")
        <!-- resources/views/components/departments-form.blade.php -->
        <form method="POST" action="{{ route('departments.update', $department->id) }}" class="needs-validation p-5" novalidate>
            @csrf
            <x-departments-form-fields :department="$department" :res="$res" :edit="false"/>
        </form>
    @endsection  
    
</x-app-layout>