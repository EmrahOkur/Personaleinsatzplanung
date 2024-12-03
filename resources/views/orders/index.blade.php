@extends('layouts.app')

@section('header')
    Aufträge
@endsection
@section('main')
    <div class="container pt-3">
        <div class="row mb-4 d-flex justify-content-end">           
            <div class="col-md-6" align="right">
                <form method="get" action="/orders/create">
                    <button type="submit" class="btn btn-primary">Neu anlegen</button>
                </form>
            </div>
        </div>
        
    </div>
@endsection