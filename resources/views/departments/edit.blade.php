<x-app-layout>

    @section("header")
        <span class="ls-3 ps-3 fs-4">Abteilung/Bereich ändern</span>
    @endsection
    
    @section("main")
        <!-- resources/views/components/departments-edit-form.blade.php -->        
        <x-departments-edit-form-fields :department="$department" :res="$res" :edit="false"/>        
    @endsection  
    
</x-app-layout>