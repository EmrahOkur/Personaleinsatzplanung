@extends('layouts.app')

@section('header')
    Aufträge
@endsection
@section('main')
    
<div class="card mt-3">
    <div class="card-body">
        <div class="d-flex justify-content-end align-items-center">
            <form method="get" action="/orders/create">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> 
                    Neu anlegen
                </button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Datum/Zeit</th>
                        <th>Firma</th>
                        <th>Kunde</th>
                        <th>Adresse</th>
                        <th>Mitarbeiter</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="align-middle">{{ $order->appointment_date->format('d.m.Y') }} {{ $order->appointment_time->format('H:i') }}</td>
                            <td class="align-middle">{{ $order->customer->companyname }}</td>
                            <td class="align-middle">{{ $order->customer->nachname }}, {{ $order->customer->vorname }}</td>
                            <td class="align-middle p-1"><div class="d-flex flex-column p-0 m-0">{{ $order->customer->address->street }} {{ $order->customer->address->house_number }}</div><div>{{ $order->customer->address->zip_code }} {{ $order->customer->address->city }}</div></td>
                            <td class="align-middle">{{ $order->employee->first_name .' ' .$order->employee->last_name }}</td>                           
                            <td class="text-end align-middle">
                                <button variant="primary" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i> Bearbeiten
                                </button>                               
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Keine Aufträge vorhanden</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
        
    </div>
@endsection