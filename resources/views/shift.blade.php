@extends('layouts.app')

@section('header')
    <section class=" header shift-header d-flex flex-column align-items-center" id="shift-header" data-loggeduserid = "{{Auth::id()}}">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Schichten') }}
            <div class="">

        </h2>
        <div class="week-navigation">
            <button id="prev-week"  class="btn btn-primary">Vorherige Woche</button>
            <span id="week-label" class="mx-2"></span>
            <button id="next-week"  class="btn btn-primary">Nächste Woche</button>
        </div>
    </section>
@endsection
@section('main')
    <div class="container-fluid" style="position:relative;">
    <table class="table table-bordered">
    <div class="spinner-border" id="shift-spinner" role="status">
        <span class="sr-only">Loading...</span>
    </div>
        <thead>
            <tr>
            <th scope="col" class="col-1">Name</th>
            <th scope="col" class="col-1">
                Montag<br><span class="date-label" id="date-monday"></span>
            </th>
            <th scope="col" class="col-1">
                Dienstag<br><span class="date-label" id="date-tuesday"></span>
            </th>
            <th scope="col" class="col-1">
                Mittwoch<br><span class="date-label" id="date-wednesday"></span>
            </th>
            <th scope="col" class="col-1">
                Donnerstag<br><span class="date-label" id="date-thursday"></span>
            </th>
            <th scope="col" class="col-1">
                Freitag<br><span class="date-label" id="date-friday"></span>
            </th>
            <th scope="col" class="col-1">
                Samstag<br><span class="date-label" id="date-saturday"></span>
            </th>
            <th scope="col" class="col-1">
                Sonntag<br><span class="date-label" id="date-sunday"></span>
            </th>
            </tr>
        </thead>
  <tbody id="shift-tbody">
<!--    @foreach ( $employees as $employee )
        <tr id="shift-tr">
            <th scope="row">{{$employee->first_name}} {{$employee->last_name}}</th>
            <td style="position:relative"><p  id="rowMonday" data-id ="{{$employee->id}}" class="shiftDay" data-bs-toggle="modal" data-bs-target="#shiftModal" style=position:relative;width:100%;height:100%;left:0;top:0;></p></td>
            <td style="position:relative"><p  id="rowTuesday" data-id ="{{$employee->id}}" class="shiftDay" data-bs-toggle="modal" data-bs-target="#shiftModal" style=position:relative;width:100%;height:100%;left:0;top:0;></p></td>
            <td style="position:relative"><p  id="rowWednesday" data-id ="{{$employee->id}}" class="shiftDay" data-bs-toggle="modal" data-bs-target="#shiftModal" style=position:relative;width:100%;height:100%;left:0;top:0;></p></td>
            <td style="position:relative"><p  id="rowThursday" data-id ="{{$employee->id}}" class="shiftDay" data-bs-toggle="modal" data-bs-target="#shiftModal" style=position:relative;width:100%;height:100%;left:0;top:0;></p></td>
            <td style="position:relative"><p  id="rowFriday" data-id ="{{$employee->id}}" class="shiftDay" data-bs-toggle="modal" data-bs-target="#shiftModal" style=position:relative;width:100%;height:100%;left:0;top:0;></p></td>
            <td style="position:relative"><p  id="rowSaturday" data-id ="{{$employee->id}}" class="shiftDay" data-bs-toggle="modal" data-bs-target="#shiftModal" style=position:relative;width:100%;height:100%;left:0;top:0;></p></td>
            <td style="position:relative"><p  id="rowSunday" data-id ="{{$employee->id}}" class="shiftDay" data-bs-toggle="modal" data-bs-target="#shiftModal" style=position:relative;width:100%;height:100%;left:0;top:0;></p></td>
        </tr>
    @endforeach
-->
  </tbody>
</table>
</div>
<script>
    let userId = document.getElementById("shift-header").dataset.loggeduserid;

    function loadAllEmployeesNames(employees){
        let shift_tbody = document.getElementById("shift-tbody");
        employees.forEach(employee => {
            const shift_tr = document.createElement("tr"); 
            shift_tr.innerHTML = `
                <th scope="row">${employee.first_name} ${employee.last_name}</th>
                <td style="position:relative"><p  id="rowMonday" data-id ="${employee.id}" class="shiftDay" data-bs-toggle="modal" data-bs-target="#shiftModal" style=position:relative;width:100%;height:100%;left:0;top:0;></p></td>
                <td style="position:relative"><p  id="rowTuesday" data-id ="${employee.id}" class="shiftDay" data-bs-toggle="modal" data-bs-target="#shiftModal" style=position:relative;width:100%;height:100%;left:0;top:0;></p></td>
                <td style="position:relative"><p  id="rowWednesday" data-id ="${employee.id}" class="shiftDay" data-bs-toggle="modal" data-bs-target="#shiftModal" style=position:relative;width:100%;height:100%;left:0;top:0;></p></td>
                <td style="position:relative"><p  id="rowThursday" data-id ="${employee.id}" class="shiftDay" data-bs-toggle="modal" data-bs-target="#shiftModal" style=position:relative;width:100%;height:100%;left:0;top:0;></p></td>
                <td style="position:relative"><p  id="rowFriday" data-id ="${employee.id}" class="shiftDay" data-bs-toggle="modal" data-bs-target="#shiftModal" style=position:relative;width:100%;height:100%;left:0;top:0;></p></td>
                <td style="position:relative"><p  id="rowSaturday" data-id ="${employee.id}" class="shiftDay" data-bs-toggle="modal" data-bs-target="#shiftModal" style=position:relative;width:100%;height:100%;left:0;top:0;></p></td>
                <td style="position:relative"><p  id="rowSunday" data-id ="${employee.id}" class="shiftDay" data-bs-toggle="modal" data-bs-target="#shiftModal" style=position:relative;width:100%;height:100%;left:0;top:0;></p></td>
            `;
            shift_tbody.appendChild(shift_tr);
        })
        updateDateLabels();
    }

    function resetAllButtons(){
        const buttons = document.querySelectorAll('.shiftDay');
        buttons.forEach(button =>{
            button.textContent = ``;
        })
    }

    function updateShift(){
        // test
        resetAllButtons();
        let table = document.getElementsByClassName('table-bordered')[0];
        let header = document.getElementById('shift-header');
        let spinner = document.getElementById('shift-spinner');
        spinner.style.display = "block";
        table.style.display = "none";
        header.style.display ="none";
        fetch(`/shifts/getUsersWithShifts/${userId}`)
        .then(response => {
            if(!response.ok){
                throw new Error('Netzwerkantowrt war nicht ok');
            }
            return response.json()
        })
        .then(users => {
            loadAllEmployeesNames(users)
            console.log("users ", users)
            const buttons = document.querySelectorAll('.shiftDay');
            buttons.forEach(button => {
                const dataDate = button.getAttribute('data-date');
                const dataUserId = button.getAttribute('data-id');
                // Falls das Date Format verwendet wird
                // let dataDateDateForLaravel = formatDateForLaravel(dataDate);
                // Suche nach dem Benutzer, dessen Schicht mit dem data-date übereinstimmt
                users.forEach(user => {
        
                    if (Array.isArray(user.shifts)) {
                    user.shifts.forEach(shift => {
                        // Datum trifft zu
                        console.log("user.id ",user.id," ",dataUserId," shift.date_shift",shift.date_shift, " ",dataDate)
                        if(user.id == dataUserId && shift.date_shift == dataDate ){
                            button.innerHTML += `${shift.start_time} - ${shift.end_time} <br>`;
                            console.log('assignedUser ' + user.id + ' shift.date_shift ' + shift.date_shift)
                        }
                        
                    });
                }else{console.log("Schichten ", user.shifts)}
                });
                
            });
            // Schicht wurde geladen, Spinner soll nicht mehr sichtbar sein
            table.style.display = "table";
            spinner.style.display = "none";
            header.style.display = "block";
            
        })
    .catch(error => console.error('Fehler:', error));
    }

    function formatDateForLaravel(dateString) {
    // Zerlege das Datum im Format 'DD.MM.YYYY'
    const parts = dateString.split('.');
    if (parts.length !== 3) {
        console.error('Invalid date format:', dateString);
        return null; // Beenden, wenn das Datum ungültig ist
    }

    const day = parts[0].padStart(2, '0'); // Tag
    const month = parts[1].padStart(2, '0'); // Monat
    const year = parts[2]; // Jahr

    // Formatiere das Datum in 'YYYY-MM-DD'
    const formattedDate = `${year}-${month}-${day}`;
    return formattedDate;
    }
    
    function shiftModal(event,user_id){
        let data_date = event.target.dataset.date;

        const laravelDate = formatDateForLaravel(data_date);

        document.getElementById('date_shift').value = laravelDate;
        document.getElementById('user_id').value = user_id;
    }


    let currentDate = new Date();
    
    // Setze das Datum auf den Montag dieser Woche
    const dayOfWeek = currentDate.getDay();
    const daysToMonday = dayOfWeek === 0 ? -6 : 1 - dayOfWeek; // 0 = Sonntag, also zurück zu -6
    currentDate.setDate(currentDate.getDate() + daysToMonday);

    const weekLabel = document.getElementById('week-label');
    const shiftTableBody = document.getElementById('shift-table-body');

    function updateWeekLabel() {
        const options = { year: 'numeric', month: 'numeric', day: 'numeric' };
        const startOfWeek = currentDate.toLocaleDateString('de-DE', options);
        const endOfWeek = new Date(currentDate.getTime() + 6 * 24 * 60 * 60 * 1000).toLocaleDateString('de-DE', options);
        weekLabel.textContent = `${startOfWeek} - ${endOfWeek}`;
    }

    function updateDateLabels() {
        const mondayDate = currentDate.toLocaleDateString('de-DE');
        const tuesdayDate = new Date(currentDate.getTime() + 1 * 24 * 60 * 60 * 1000).toLocaleDateString('de-DE');
        const wednesdayDate = new Date(currentDate.getTime() + 2 * 24 * 60 * 60 * 1000).toLocaleDateString('de-DE');
        const thursdayDate = new Date(currentDate.getTime() + 3 * 24 * 60 * 60 * 1000).toLocaleDateString('de-DE');
        const fridayDate = new Date(currentDate.getTime() + 4 * 24 * 60 * 60 * 1000).toLocaleDateString('de-DE');
        const saturdayDate = new Date(currentDate.getTime() + 5 * 24 * 60 * 60 * 1000).toLocaleDateString('de-DE');
        const sundayDate = new Date(currentDate.getTime() + 6 * 24 * 60 * 60 * 1000).toLocaleDateString('de-DE');

        document.getElementById('date-monday').textContent = mondayDate;
        document.getElementById('date-tuesday').textContent = tuesdayDate;
        document.getElementById('date-wednesday').textContent = wednesdayDate;
        document.getElementById('date-thursday').textContent = thursdayDate;
        document.getElementById('date-friday').textContent = fridayDate;
        document.getElementById('date-saturday').textContent = saturdayDate;
        document.getElementById('date-sunday').textContent = sundayDate;

        function setDataDate(arr,date){
            var index = 0, length = arr.length;
            for ( ; index < length; index++) {          
                arr[index].setAttribute('data-date',date)
                }
            }
        setDataDate(document.querySelectorAll('#rowMonday'),mondayDate)
        setDataDate(document.querySelectorAll('#rowTuesday'),tuesdayDate)
        setDataDate(document.querySelectorAll('#rowWednesday'),wednesdayDate)
        setDataDate(document.querySelectorAll('#rowThursday'),thursdayDate)
        setDataDate(document.querySelectorAll('#rowFriday'),fridayDate)
        setDataDate(document.querySelectorAll('#rowSaturday'),saturdayDate)
        setDataDate(document.querySelectorAll('#rowSunday'),sundayDate)
    }

    function updateShifts() {
        // Hier kannst du die Logik einfügen, um die Schichten entsprechend der Woche zu laden
        // Beispielinhalt:
        shiftTableBody.innerHTML = `
            <tr>
                <th scope="row">Veri</th>
                <td>08:00 - 16:00</td>
                <td></td>
                <td>12:00 - 20:00</td>
                <td></td>
                <td></td>
                <td>10:00 - 18:00</td>
                <td></td>
            </tr>
            <tr>
                <th scope="row">Maurer</th>
                <td></td>
                <td>08:00 - 16:00</td>
                <td></td>
                <td></td>
                <td>12:00 - 20:00</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th scope="row">Müller</th>
                <td></td>
                <td></td>
                <td>08:00 - 16:00</td>
                <td></td>
                <td></td>
                <td>10:00 - 18:00</td>
                <td></td>
            </tr>
        `;
    }

    document.getElementById('prev-week').addEventListener('click', function () {
        currentDate.setDate(currentDate.getDate() - 7);
        updateWeekLabel();
        updateDateLabels();
        updateShift();

        //updateShifts();
    });

    document.getElementById('next-week').addEventListener('click', function () {
        currentDate.setDate(currentDate.getDate() + 7);
        updateWeekLabel();
        updateDateLabels();
        updateShift();
        //updateShifts();
    });

    // Initialisiere die Anzeige
    // Schicht wird geupdatet 
    updateWeekLabel();
    updateDateLabels();
    updateShift()
    //updateShifts();

</script>
@endsection

