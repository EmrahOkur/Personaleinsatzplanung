<x-app-layout>
    @section('header')
        {{ __('Admin-Einstellungen') }}
    @endsection

    @section('main')

    <div class="container">
        <form action="{{ route('adminsettings.change')}}" method="POST">
            <div class="row">
            @csrf
            @method('POST')
            <div class="col-md-4 mt-3">
                <div class="form-group">
                    <label for="sidebar_visible">Sidebar anzeigen?</label>
                    <select name="sidebar_visible" id="sidebar_visible" class="form-control">
                        <option value="1" {{ $settings->sidebar_visible == 1 ? 'selected' : '' }} >Ja</option>
                        <option value="0" {{ $settings->sidebar_visible == 0 ? 'selected' : '' }} >Nein</option>
                    </select>
                </div>
            </div>

            <div class="col-md-4 mt-3">
                <div class="form-group">
                    <label for="max_week_planning">Maximale Wochenplanung (in Tagen)</label>
                    <input type="number" name="max_week_planning" id="max_week_planning" class="form-control" value="{{$settings->max_week_planning}}" min="1" max="1000">
                </div>
            </div>

            <div class="col-md-4 mt-3">
                <div class="form-group">
                    <label for="show_employees">Sollen Mitarbeiter in den Schichten angezeigt werden?</label>
                    <select name="show_employees" id="show_employees" class="form-control">
                        <option value="1" {{ $settings->show_employees == 1 ? 'selected' : '' }} >Ja</option>
                        <option value="0" {{ $settings->show_employees == 0 ? 'selected' : '' }} >Nein</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3 mt-3">
                <button type="submit" class="btn btn-primary">Speichern</button>
            </div>
        </div>
        </form>
    </div>
    @endsection
</x-app-layout>
