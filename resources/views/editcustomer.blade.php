<x-app-layout>
    <!-- Well begun is half done. - Aristotle -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kundendaten bearbeiten') }}
        </h2>
    </x-slot>
    <form action="{{route('customers.update',$customer->id)}}" class="form-control" method="POST">
        @csrf
        @method('PUT')
    <div class="container editcustomer">
        <div class="row">
                <div class="col-md-6">
                    <label for="editVorname" class="form-label">Vorname</label>
                    <input type="text" value="{{$customer->vorname}}" name="editVorname" id="editVorname" class="form-control">
                </div>
                <div class="col-md-6 mb-4">
                    <label for="editNachname" class="form-label">Nachname</label>
                    <input type="text" value="{{$customer->nachname}}" name="editNachname" id="editNachname" class="form-control">
                </div>
                <div class="col-md-12 mb-4">
                    <label for="editOrt" class="form-label">Ort</label>
                    <input type="text" value="{{$customer->ort}}" name="editOrt" id="editOrt"class="form-control">
                </div>
                <div class="col-md-2 ">
                    <button type="submit" class="btn btn-sm btn-primary">Speichern</button>
                </div>
                
        </div>
    </div>
    </form>
</x-app-layout>
