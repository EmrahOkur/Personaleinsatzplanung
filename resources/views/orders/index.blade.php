@extends('layouts.app')

@section('header')
    Aufträge
@endsection
@section('main')
    
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-end align-items-center">
            <form method="get" action="/orders/create">
                <button type="submit" class="btn btn-primary">Neu anlegen</button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Zeit</th>
                        <th>Kunde</th>
                        <th>Firma</th>
                        <th>Mitarbeiter</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->appointment_date->format('d.m.Y') }}</td>
                            <td>{{ $order->appointment_time->format('H:i') }}</td>
                            <td>{{ $order->customer->nachname }}, {{ $order->customer->vorname }}</td>
                            <td>{{ $order->customer->companyname }}</td>
                            <td>{{  $order->employee->first_name .' ' .$order->employee->last_name }}</td>
                           
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('orders', $order) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('orders', $order) }}" 
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Keine Aufträge vorhanden</td>
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