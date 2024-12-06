<x-app-layout>
    @section('header')
        Urlaubsübersicht
    @endsection

    @section('main')
        @php
            $status = [
                'pending' => 'Freigabe ausstehend',
                'rejected' => 'Urlaub abgelehnt',
                'accepted' => 'Urlaub erlaubt',
            ];
        @endphp


    <div class="p-3">    
        <div class="row">

            <div class="col-sm">
                <table class="col-sm table table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Abwesend am</th>
                            <th>Status</th>
                            <th>Vorname</th>      
                            <th>Nachname</th>  
                            <th></th>      
                            <th></th>    
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="urlaubTableBody">
                        @foreach($urlaubs as $urlaub)
                            <tr>
                                <td>{{ $urlaub->datum }}</td>
                                <td>{{ $status[$urlaub->status] }}</td>
                                <td>{{ $urlaub->first_name }}</td>
                                <td>{{ $urlaub->last_name }}</td>
                                <td class="pe-3">
                                    <form action="{{ route('managerUrlaubs.genehmigen') }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-primary btn-sm" >
                                            <i class="bi bi-trash" ></i>Genehmigen 
                                        </button>
                                        <input type="hidden" name="vacation_id" value="{{$urlaub->urlaub_id}}">
                                    </form>
                                </td>

                                <td class="pe-3">
                                    <form action="{{ route('managerUrlaubs.ablehnen', $urlaub->urlaub_id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-primary btn-sm" >
                                            <i class="bi bi-trash" ></i>Ablehnen 
                                        </button>
                                        <input type="hidden" name="vacation_id" value="{{$urlaub->urlaub_id}}">
                                    </form>
                                </td>
                                
                                <td class="pe-3">
                                    <form action="{{ route('managerUrlaubs.loeschen', $urlaub->urlaub_id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Möchten Sie diesen Eintrag wirklich löschen?')">
                                            <i class="bi bi-trash" ></i>Löschen 
                                        </button>
                                    </form>
                                </td>

                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

            <script>      
            </script>    
    </div>
    @endsection
</x-app-layout>


