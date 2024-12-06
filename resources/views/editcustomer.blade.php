<x-app-layout>
    <!-- Well begun is half done. - Aristotle -->
    @section('header')
            {{ __('Kunden bearbeiten') }}
        
    @endsection
    @section('main')
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
                <div class="col-md-6 mb-4">
                    <label for="editCompanyname" class="form-label">Unternehmen</label>
                    <input type="text" value="{{$customer->companyname}}" name="editCompanyname" id="editCompanyname"class="form-control">
                </div>
                <div class="col-md-6 mb-4">
                    <label for="editStreet" class="form-label">Straße</label>
                    <input type="text" value="{{$customer->street}}" name="editStreet" id="editStreet"class="form-control">
                </div>
                <div class="col-md-6 mb-4">
                    <label for="editHousenumber" class="form-label">Hausnummer</label>
                    <input type="text" value="{{$customer->house_number}}" name="editHousenumber" id="editHousenumber"class="form-control">
                </div>
                <div class="col-md-6 mb-4">
                    <label for="editZip" class="form-label">Postleitzahl</label>
                    <input type="text" value="{{$customer->zip_code}}" name="editZip" id="editZip"class="form-control">
                </div>
                <div class="col-md-6 mb-4">
                    <label for="editCity" class="form-label">Stadt</label>
                    <input type="text" value="{{$customer->city}}" name="editCity" id="editCity"class="form-control">
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-2 ">
                    <button type="submit" class="btn btn-sm btn-primary">Speichern</button>
                </div>
                
        </div>
    </div>
    </form>
    @endsection
</x-app-layout>
