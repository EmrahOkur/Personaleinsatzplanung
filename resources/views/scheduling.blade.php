
@extends('layouts.app')
@section('header')
    Arbeitsplan erstellen für 
    <span class="font-semibold text-xl text-gray-800 leading-tight" id="schedule-h2">

</span>
@endsection
@section('main')
<section class="header d-flex flex-column align-items-center mt-2 mb-3" id="schedule-header" data-loggeduserid = "{{Auth::id()}}" data-showemployees="{{$settings->show_employees}}" >
    <div class="month-navigation">
        <button id="prev-week" class="btn btn-primary">Vorherige Woche</button>
        <span id="week-label" class="mx-2"></span>
        <button id="next-week" class="btn btn-primary">Nächste Woche</button>
    </div>
</section>
    <!-- 2. Modalfenster -->
    <div class="modal fade" id="secondModalSchedule" tabindex="-1" aria-labelledby="secondModalScheduleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" >
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="secondModalScheduleLabel">Mitarbeiter auswählen</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="scheduleBody">
        <div class="col-md-12">
            <p id="modal-schedule-date-2"></p>
            <p id="modal-schedule-date-start-2"></p>
            <p id="modal-schedule-date-end-2"></p>
            <p id="modal-error-message-2"></p>
            <p id="schedule-amount"></p>
            <br>
            <form id="scheduleFormModal2" action="" method="POST">
            <!-- Dynamische Checkboxes hier -->
            </form>
            
        </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="deleteShift(event)" id="delteShift" class="btn btn-danger" data-bs-dismiss="modal">Löschen</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
            <button type="button" form="edit-shift" id="saveAssignedEmployees" class="btn btn-primary" >Speichern</button>
        </div>
        </div>
    </div>
    </div>

    <!-- 1. Modal Schicht hinzufügen-->
    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="customerScheduleModalLabel">Schicht hinzufügen </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="1">
            @csrf
            <div class="modal-body"> 
                <div class="container">
                    <p id="schedule-date"></p> 
                    <div class="row" id="firstModalRow">
                        <div class="col-md-12">
                            <label for="name"> Schichtbezeichnung</label>
                            <input type="text" id="name_shift" name="name" class="form-control">
                        </div>
                        <div class="col-md-6">
                        <label for="start_shift"> Beginn</label>
                            <input type="time" id="start_shift" name="start_shift" class="form-control" value="12:00">
                        </div>
                        <div class="col-md-6">
                            <label for="end_shift"> Ende</label>
                            <input type="time" id="end_shift" name="end_shift" class="form-control" value="15:00">
                        </div>
                        <div class="col-md-12">
                            <label for="employee_schedule"> Anzahl benötigter Mitarbeiter</label>
                            @if (count($employees) > 0)
                            <input type="number" id="employee_schedule" name="employee_schedule" class="form-control" value="1" min="1" max="{{count($employees)}}">
                            @else
                            <p>Keine Mitarbeiter vorhanden</p>
                            @endif
                        </div>
                        <input type="hidden" name="date_shift" id="date_shift" >
                        <input type="hidden" name="user_id" id="user_id" >
                    </div>
                    <br>

                    <input class="form-check-input" name="checkbox_for_calender" id="checkbox_for_calender" type="checkbox">
                    <label class="form-check-label" for="checkbox_for_calender">
                    Mehrere Schichten erstellen?
                    </label>

                    <div class="mehrere-Schichten" id="multiple_shifts">
                        <label for="shifts_start_date"></label>
                        <input type="date" name="shifts_start_date" for="shifts_start_date" id="shifts_start_date" required>
                        <label for="shifts_end_date"></label>
                        <input type="date" name="shifts_end_date" for="shifts_end_date" id="shifts_end_date" required>
                        <br>

                        <label class="multiple-shifts-workdays-checkbox" for="shifts_monday">Montag
                        <input class="form-check-input mr-2 multiple-shifts-workdays" data-workday="Montag" type="checkbox" name="shifts_monday" id="shifts_monday" >
                        </label>
                        <label class="multiple-shifts-workdays-checkbox" for="shifts_tuesday">Dienstag
                        <input class="form-check-input mr-2 multiple-shifts-workdays" data-workday="Dienstag" type="checkbox" name="shifts_tuesday"  >
                        </label>
                        <label class="multiple-shifts-workdays-checkbox" for="shifts_wednesday">Mittwoch
                        <input class="form-check-input mr-2 multiple-shifts-workdays" data-workday="Mittwoch" type="checkbox" name="shifts_wednesday"  >
                        </label>
                        <label class="multiple-shifts-workdays-checkbox" for="shifts_thursday">Donnerstag
                        <input class="form-check-input mr-2 multiple-shifts-workdays" data-workday="Donnerstag" type="checkbox" name="shifts_thursday"  >
                        </label>
                        <label class="multiple-shifts-workdays-checkbox" for="shifts_friday">Freitag
                        <input class="form-check-input mr-2 multiple-shifts-workdays" data-workday="Freitag" type="checkbox" name="shifts_friday"  >
                        </label>
                        <label class="multiple-shifts-workdays-checkbox" for="shifts_saturday">Samstag
                        <input class="form-check-input mr-2 multiple-shifts-workdays" data-workday="Samstag" type="checkbox" name="shifts_saturday"  >
                        </label>
                        <label class="multiple-shifts-workdays-checkbox" for="shifts_sunday">Sonntag
                        <input class="form-check-input mr-2 multiple-shifts-workdays" data-workday="Sonntag" type="checkbox" name="shifts_sunday"  >
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                <!-- <button type="submit" onclick="secondScheduleModalDate(event)" form="edit-shift" class="btn btn-primary" data-bs-toggle="modal" id="firstModalSchedule">Weiter</button> -->
                <button type="button" class="btn btn-primary " onclick="addShiftToSchedule()" data-bs-dismiss="modal" id="firstModalSchedule">Speichern</button>
            </div>
            </form>
            </div>
        </div>
    </div>
    <div class="container-fluid" style="position:relative;">
        <div class="row">
            @if ($settings->sidebar_visible)
            <div class="col-md-1">
                <div class="employee-sidebar" id="schedule-employee-sidebar">
                    
                </div>
            </div>
            
            <div class="col-md-11">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col" class="col-1">Montag</th>
                        <th scope="col" class="col-1">Dienstag</th>
                        <th scope="col" class="col-1">Mittwoch</th>
                        <th scope="col" class="col-1">Donnerstag</th>
                        <th scope="col" class="col-1">Freitag</th>
                        <th scope="col" class="col-1">Samstag</th>
                        <th scope="col" class="col-1">Sonntag</th>
                    </tr>
                </thead>
                <tbody id="schedule-body">
                    <!-- Dynamisch generierte Tage werden hier eingefügt -->
                </tbody>
            </table>
            </div>
            @else
            <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col" class="col-1">Montag</th>
                        <th scope="col" class="col-1">Dienstag</th>
                        <th scope="col" class="col-1">Mittwoch</th>
                        <th scope="col" class="col-1">Donnerstag</th>
                        <th scope="col" class="col-1">Freitag</th>
                        <th scope="col" class="col-1">Samstag</th>
                        <th scope="col" class="col-1">Sonntag</th>
                    </tr>
                </thead>
                <tbody id="schedule-body">
                    <!-- Dynamisch generierte Tage werden hier eingefügt -->
                </tbody>
            </table>
            </div>
            @endif
        </div>
    </div>
    <style>
            #calendar {
                width: 100%;
                height: 600px;
                max-width: 800px;
                margin: 0 auto;
            }
            .multiple-shifts-workdays-checkbox {
                padding: 2px;
                border: 1px solid grey;
                border-radius: 4px;
                margin-top: 10px;
                min-width: 100px;
            }
        </style>
    
    <script>
        
        let userId = document.getElementById("schedule-header").dataset.loggeduserid;
        showMultipleShiftOptions();
        function showMultipleShiftOptions(){
            let checkbox_for_calender = document.getElementById("checkbox_for_calender");
            let element = document.getElementById('multiple_shifts');
            checkbox_for_calender.addEventListener("change", function () {
                if(this.checked){
                    element.style.display = "block";
                }
                else{
                    element.style.display = "none";
                }
            });
        }

        function markHolidays(){
            fetch('/urlaubs/feiertage')
                .then(response => response.json())
                .then(data => {
                    console.log("update")
                    let events = [];
                    for (const [name, details] of Object.entries(data)) {
                        events.push({
                            title: name,
                            date: formateDate(details.datum),
                            color: '#FA8072',
                        });
                    }
                    let shifts = document.querySelectorAll(".shift-button");
                    let shift_dates = [];
                    shifts.forEach(shift => {
                        shift_dates.push(shift.getAttribute("data-date"));
                    })
                    events.forEach(event => {
                        if(shift_dates.includes(event.date)){
                            document.getElementById(event.date).closest("td").style.backgroundColor = event.color;
                            console.log("found",document.getElementById(event.date).closest(".td-wrapper-flex"))
                        }
                    })
                })
                .catch(error => console.log(error));
        }

        function calculateShiftHours(startTime,endTime) {
            // Wandle die Zeiten in Minuten um
            let startTimeParts = startTime.split(":");
            let endTimeParts = endTime.split(":");

            // Berechne die Gesamtminuten für Startzeit und Endzeit
            let startMinutes = parseInt(startTimeParts[0]) * 60 + parseInt(startTimeParts[1]);
            let endMinutes = parseInt(endTimeParts[0]) * 60 + parseInt(endTimeParts[1]);

            // Berechne die Differenz in Minuten
            let differenceInMinutes = endMinutes - startMinutes;

            // Wandle Minuten in Dezimalstunden um
            let shiftHours = differenceInMinutes / 60;

            // Ausgabe der berechneten Stunden (zum Beispiel 2.5 Stunden)

            return shiftHours;
        }

        function formateDate(laravelDate){
            let formattedDate = moment(laravelDate).format('D.M.YYYY'); 
            return formattedDate;
        }
        function showEmployeesFromDepartment(){
            let week = document.getElementById('week-label').textContent;
            const regex = /^(.+?)\s*-\s*(.+)$/;
            let startAndEndDate = week.match(regex);
            let startDate = startAndEndDate[1];
            let endDate = startAndEndDate[2];
            
            fetch(`/departments/getEmployeesFromDepartmentByUser/${userId}/${startDate}/${endDate}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("schedule-h2").textContent = "Abteilung: " + data.department.name;
                    let departmentEmployees = data.departmentEmployees;
                    let sidebar = document.getElementById('schedule-employee-sidebar');
                    sidebar.innerHTML = "";
                    let sidebarEmployees;
                    departmentEmployees.forEach(employee => {
                        let countTime = 0;
                        employee.shifts.forEach(shift =>{
                            countTime += parseFloat(shift.shift_hours);
                        })
                        sidebarEmployees = document.createElement("p");
                        sidebarEmployees.innerHTML = employee.first_name + " " + employee.last_name + " "+countTime+ "/" +  employee.working_hours;
                        sidebar.appendChild(sidebarEmployees);
                    });

                    
                })
        }
        let currentDate = new Date();
                // Diese Funktion aktualisiert die Woche und zeigt die richtigen Daten an
        function updateWeek() {
            const weekLabel = document.getElementById('week-label');

            // Berechne den ersten Tag der Woche
            const startOfWeek = new Date(currentDate);
            startOfWeek.setDate(currentDate.getDate() - currentDate.getDay() + 1); // Setze auf Montag der aktuellen Woche
            // Zeigt den Zeitraum der Woche an
            const endOfWeek = new Date(startOfWeek);
            endOfWeek.setDate(startOfWeek.getDate() + 6); // Sonntag der aktuellen Woche

            // Formatierung der Woche
            let formattedWeek = `${startOfWeek.toLocaleDateString('de-DE')} - ${endOfWeek.toLocaleDateString('de-DE')}`;
            weekLabel.textContent = formattedWeek;
            showEmployeesFromDepartment();
            // Schichten werden geladen
            fetch('/scheduling/getShifts')
                .then(response => response.json())
                .then(data => {
            
            let showingEmployees = document.getElementById('schedule-header').dataset.showemployees;
            const scheduleBody = document.getElementById('schedule-body');
            scheduleBody.innerHTML = ''; // Leere den bestehenden Plan

            // Erstelle eine neue Zeile für die Woche
            let row = document.createElement('tr');
            let dateCounter = startOfWeek;

            // Füge für jeden Wochentag eine Zelle hinzu
            for (let i = 0; i < 7; i++) {
                const dayCell = document.createElement('td');
                const tddiv = document.createElement('div');
                const tdwrapper = document.createElement('div')
                tdwrapper.setAttribute("class", "td-wrapper-flex");
                const dateElement = document.createElement('span');
                const addshiftButton = document.createElement('button');
                addshiftButton.textContent = "Hinzufügen";
                addshiftButton.setAttribute("class", "btn btn-primary padding-5px shift-button");
                addshiftButton.setAttribute("data-bs-target", "#scheduleModal"); 
                addshiftButton.setAttribute("data-bs-toggle", "modal");
                addshiftButton.setAttribute("data-date", `${dateCounter.getDate()}.${dateCounter.getMonth() + 1}.${dateCounter.getFullYear()}`);
                addshiftButton.setAttribute("onclick", "firstScheduleModalDate(event)");
                dateElement.textContent = `${dateCounter.getDate()}.${dateCounter.getMonth() + 1}.${dateCounter.getFullYear()}`;
                // Schicht wird für den Tag angezeigt, falls vorhanden
                if(data != 'empty'){
                let shiftsForDay = data.filter(shift => formateDate(shift.date_shift) === dateElement.textContent);

                shiftsForDay.forEach(shift => {
                    const shiftDiv = document.createElement('div');
                    shiftDiv.classList.add('list-group');
                    let employees_arr = [];
                    // Alle Mitarbeiter der zugeordneten Schicht werden gezählt
                    shift.employees.forEach(employee => {
                        employees_arr.push(employee);
                    })
                    let employeeInfoWithoutNamesHTML = `
                        <p class="list-group-item">${shift.start_time} - ${shift.end_time}</p>
                        <p class="list-group-item list-employees">Mitarbeiter: ${employees_arr.length}/${shift.amount_employees}</p>
                        <button class="btn btn-success" onclick="secondScheduleModal(event)" data-shiftid = ${shift.id} data-requiredemployees = ${shift.amount_employees} data-bs-target="#secondModalSchedule" id="secondModalAddEmployees" data-bs-toggle="modal">Bearbeiten </button>
                    `;
                    let employeeInfoHTML = `
                        <p class="list-group-item">${shift.start_time} - ${shift.end_time}</p>
                        <p class="list-group-item list-employees">Mitarbeiter: ${employees_arr.length}/${shift.amount_employees}</p>
                         <p class="user-list"> ${employees_arr ? employees_arr.map(employee => employee.first_name +" " + employee.last_name).join(', ') : "Keine Mitarbeiter zugewiesen"} </p>
                        <button class="btn btn-success" onclick="secondScheduleModal(event)" data-shiftid = ${shift.id} data-requiredemployees = ${shift.amount_employees} data-bs-target="#secondModalSchedule" id="secondModalAddEmployees" data-bs-toggle="modal">Bearbeiten </button>
                    `;
                    // Abfrage, ob Mitarbeiter angezeigt werden sollen. Wird in den Settings bestimmt.
                    console.log("Interessant",showingEmployees ? "true":"false")
                    if(showingEmployees == true){
                        if(shift.name){
                        shiftDiv.innerHTML = `
                            <p class="list-group-item">${shift.name}</p>
                            ${employeeInfoHTML}
                        `;
                        }else{ shiftDiv.innerHTML = employeeInfoHTML
                        }
                    }else{
                        if(shift.name){
                        shiftDiv.innerHTML = `
                            <p class="list-group-item">${shift.name}</p>
                            ${employeeInfoWithoutNamesHTML}
                        `;
                        }else{ shiftDiv.innerHTML = employeeInfoWithoutNamesHTML
                        }
                    }
                    tddiv.appendChild(shiftDiv);
                });
                }

                tddiv.setAttribute("class", "shift-list");
                tddiv.setAttribute("id", dateElement.textContent);

                // Füge das Datum und den Button zur Zelle hinzu
                tdwrapper.appendChild(dateElement);
                tdwrapper.appendChild(tddiv);
                tdwrapper.appendChild(addshiftButton);

                dayCell.appendChild(tdwrapper);

                row.appendChild(dayCell);

                // Gehe zum nächsten Tag
                dateCounter.setDate(dateCounter.getDate() + 1);
            }

            // Füge die Zeile der Tabelle hinzu
            scheduleBody.appendChild(row);


            usersListEqualHeight();
            amaountEmployeesEqualHeight();
            showUnfilledShifts();
            markHolidays();

        })
        .catch(error => {
            console.error('Fehler beim Abrufen der Schichten:', error);
            alert('Fehler beim Laden der Schichten.');
        });

    }

    

                // Eventlistener für den "Vorherige Woche" Button
                document.getElementById('prev-week').addEventListener('click', function () {
            currentDate.setDate(currentDate.getDate() - 7);
            updateWeek();
        });

        // Eventlistener für den "Nächste Woche" Button
        document.getElementById('next-week').addEventListener('click', function () {
            currentDate.setDate(currentDate.getDate() + 7);
            updateWeek();
        });

        // Initialisierung der Wochenansicht
        updateWeek();




        // Datum zum ersten Modal hinzufügen
        function firstScheduleModalDate(e){
            let checkbox = document.getElementById("checkbox_for_calender");
            if(checkbox.checked){
                checkbox.checked = false;
                console.log("was checked")
            }

            document.getElementById("multiple_shifts").style.display = 'none';
            let data_date = event.target.dataset.date;
            let schedule_p = document.getElementById("schedule-date");
            schedule_p.innerHTML = data_date; 

        }

                // Inhalt für das zweite Modalfenster
        function secondScheduleModal(e){
            document.getElementById("modal-error-message-2").innerHTML = '';
            let shiftId = e.target.dataset.shiftid;
            let requiredEmployees = e.target.dataset.requiredemployees;
            let schedule_amount_p = document.getElementById("schedule-amount");
            let delete_button = document.getElementById("delteShift");
            delete_button.setAttribute("data-shiftid", shiftId);
            schedule_amount_p.innerHTML = "Anzahl auszuwählender Mitarbeiter: " + e.target.dataset.requiredemployees; 

            // Leere das bestehende Formular
            let formContainer = document.getElementById("scheduleFormModal2");
            formContainer.innerHTML = ''; 
            // Daten werden aus der Überschrift übertragen
            let week = document.getElementById('week-label').textContent;
            const regex = /^(.+?)\s*-\s*(.+)$/;
            let startAndEndDate = week.match(regex);
            let startDate = startAndEndDate[1];
            let endDate = startAndEndDate[2];

        // Lade Mitarbeiter für diese Schicht
        fetch(`/scheduling/getEmployeesForShift/${shiftId}/${userId}/${startDate}/${endDate}`)
            .then(response => response.json())
            .then(data => {
                console.log(data.employeesWithVacation);
                data.employees.forEach(employee => {

                    let checkboxDiv = document.createElement('div');
                    checkboxDiv.classList.add('form-check');

                    let countTime = 0;
                    console.log("second modal");
                    console.log(employee.shifts, " Mitarbeiter ",employee);
                    employee.shifts.forEach(shift =>{
                        countTime += parseFloat(shift.shift_hours);
                    })

                    // Falls user in der Schicht enthalten ist
                    if(data.employeesWithVacation.includes(employee.id)){
                        checkboxDiv.innerHTML =
                        `
                        <input class="form-check-input checked" name="employee_ids[]" type="checkbox" value="${employee.id}" id="employee_${employee.id}" disabled>
                        <label class="form-check-label second-modal-employees-workingHours-label" for="employee_${employee.id}">
                            <span>${employee.first_name} ${employee.last_name}</span>  <span>${countTime}/${employee.working_hours}</span>
                        </label>
                    `;
                    }
                    else if(data.employeesInShift.includes(employee.id) ){
                        checkboxDiv.innerHTML =
                        `
                        <input class="form-check-input checked" name="employee_ids[]" type="checkbox" value="${employee.id}" id="employee_${employee.id}" checked="true">
                        <label class="form-check-label second-modal-employees-workingHours-label" for="employee_${employee.id}">
                            <span>${employee.first_name} ${employee.last_name}</span>  <span>${countTime}/${employee.working_hours}</span>
                        </label>
                    `;
                    }else{
                    checkboxDiv.innerHTML = `
                        <input class="form-check-input" name="employee_ids[]" type="checkbox" value="${employee.id}" id="employee_${employee.id}">
                        <label class="form-check-label second-modal-employees-workingHours-label" for="employee_${employee.id}">
                            <span>${employee.first_name} ${employee.last_name}</span>  <span>${countTime}/${employee.working_hours}</span>
                        </label>
                    `;
                    }
                    formContainer.appendChild(checkboxDiv);
                });
            })
            .catch(error => console.error('Fehler beim Laden der Mitarbeiter:', error));

        // Speichern der Auswahl
        document.getElementById("saveAssignedEmployees").onclick = function() {
            let selectedEmployees = [];
            document.querySelectorAll('input[name="employee_ids[]"]:checked').forEach(checkbox => {
                selectedEmployees.push(checkbox.value);
            });

            // Mitarbeiter zuweisen oder entfernen
            
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });
            $.ajax({
                url: `/scheduling/assignEmployeesToShift`,
                type: 'POST',
                data:{shift_id:shiftId, employee_ids:selectedEmployees },
                success: function(data) {
                    if(data.error) {
                    console.log("error");
                    document.getElementById("modal-error-message-2").innerHTML = data.error;
                    // alert(data.error)
                    }
                    else {
                    $('#secondModalSchedule').modal('hide');
                    updateWeek();  // Aktualisiere die Wochenansicht
                
                    }
                    
                },
                error: function(xhr, status, error) {
                    console.log(shiftId,selectedEmployees)
                    // Fehlerbehandlung: Logge die Antwort und zeige sie in der Konsole
                    alert("error");
                    console.error('Fehler bei der Anfrage:', error);
                    console.error('Status:', status);
                    console.error('Antwort:', xhr.responseText);
                    
                    // Optionale Ausgabe einer Fehlernachricht im Frontend:
                    let errorMessage = 'Ein Fehler ist aufgetreten. Bitte versuche es später noch einmal.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;  // Falls der Server eine Fehlermeldung zurückgibt
                    }
                    alert(errorMessage);  // Zeige eine allgemeine Fehlermeldung an
                }
            });
            
             /*
            fetch('/scheduling/assignEmployeesToShift', {
                method: 'POST',
                headers: {
                    //'Content-Type': 'application/json',
                    //'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({
                    shift_id: shiftId,
                    employee_ids: selectedEmployees
                })
            })
            
            .then(response => response.json())
            .then(data => {
                if(data.error){
                    console.log("error");
                    document.getElementById("modal-error-message-2").innerHTML = data.error;
                    // alert(data.error)
                }else{
                    $('#secondModalSchedule').modal('hide');
                    updateWeek();  // Aktualisiere die Wochenansicht
                
                }
            })
            .catch(error => console.error('Fehler beim Speichern:', error));
         */
        };

        }


        function resetcheckboxes(){
            let checkboxes = document.querySelectorAll('#flexCheckModal2');
            checkboxes.forEach(checkbox => {
                if(checkbox.checked){
                    checkbox.checked = false;
                }
            });
        }
        function addShiftToSchedule(){
            let shift_name = document.getElementById("name_shift").value;
            console.log("shift name",shift_name)
            let start_time = document.getElementById("start_shift").value;
            let end_time = document.getElementById("end_shift").value;
            let amount_employees = document.getElementById("employee_schedule").value;
            let date = document.getElementById("schedule-date").textContent;
            let shift_hours = calculateShiftHours(start_time,end_time);
            let multiple_shifts = document.getElementById("checkbox_for_calender");

            if(multiple_shifts.checked){
                console.log("checked");
                let shifts_start_date = document.getElementById("shifts_start_date").value;
                let shifts_end_date = document.getElementById("shifts_end_date").value;
                console.log("datum: ", shifts_end_date)
                let multiple_shifts_workdays = document.querySelectorAll(".multiple-shifts-workdays");
                let multiple_shifts_workdays_arr = Array.from(multiple_shifts_workdays);
                let filtered_multiple_shifts_workdays_arr = multiple_shifts_workdays_arr.filter((day) => day.checked);
                let checkedWorkdays = filtered_multiple_shifts_workdays_arr.map((day) => day.dataset.workday);
                if(checkedWorkdays.length === 0){
                    alert("Es müssen Wochentage ausgewählt werden, oder der Hacken bei der Checkbox 'mehrere Schichten' entfernt werden ");
                }
                else{
                $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    //'X-CSRF-TOKEN': document.querySelector('[name="_token"]').getAttribute('content')
                }
                });
                $.ajax({
                    url: `/scheduling/addMultipleShifts`,
                    type: 'POST',
                    data:{shift_name:shift_name,shifts_start_date:shifts_start_date,shifts_end_date:shifts_end_date,start_time:start_time, end_time:end_time, amount_employees:amount_employees, date:date, shift_hours:shift_hours, checkedWorkdays:checkedWorkdays  },
                    success: function(data) {
                        if(data.error){
                            alert(data.error);
                        }else{
                            updateWeek();
                        }

                    },
                    error: function(xhr, status, error) {
                        // Fehlerbehandlung: Logge die Antwort und zeige sie in der Konsole
                        alert("error");
                        console.error('Fehler bei der Anfrage:', error);
                        console.error('Status:', status);
                        console.error('Antwort:', xhr.responseText);
                        
                        // Optionale Ausgabe einer Fehlernachricht im Frontend:
                        let errorMessage = 'Ein Fehler ist aufgetreten. Bitte versuche es später noch einmal.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;  // Falls der Server eine Fehlermeldung zurückgibt
                        }
                        alert(errorMessage);  // Zeige eine allgemeine Fehlermeldung an
                    }
                });

                }
            }else{
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //'X-CSRF-TOKEN': document.querySelector('[name="_token"]').getAttribute('content')
            }
            });
            $.ajax({
                url: `/scheduling/addshifts`,
                type: 'POST',
                data:{shift_name:shift_name,start_time:start_time, end_time:end_time, amount_employees:amount_employees, date:date, shift_hours:shift_hours },
                success: function(data) {
                    updateWeek();
                    // let formattedDate = formateDate(data.date_shift);
                    // let shift_list = document.getElementById(formattedDate);
                    // let created_shift = document.createElement('div');
                    // created_shift.setAttribute("class","list-group");
                    // shift_list.appendChild(created_shift);
                    // created_shift.innerHTML = `<p class="list-group-item">${data.start_time} - ${data.end_time} </p> <p class="list-group-item list-employees"> Mitarbeiter: 0/${data.amount_employees} </p> <p class="user-list">  </p> <button class="btn btn-success" onclick="secondScheduleModal(event)" data-shiftid = ${data.id} data-requiredemployees = ${data.amount_employees} data-bs-target="#secondModalSchedule" id="secondModalAddEmployees" data-bs-toggle="modal">Bearbeiten </button>`;
                    // usersListEqualHeight();
                    // amaountEmployeesEqualHeight();
                    // showUnfilledShifts();
                },
                error: function(xhr, status, error) {
                    // Fehlerbehandlung: Logge die Antwort und zeige sie in der Konsole
                    alert("error");
                    console.error('Fehler bei der Anfrage:', error);
                    console.error('Status:', status);
                    console.error('Antwort:', xhr.responseText);
                    
                    // Optionale Ausgabe einer Fehlernachricht im Frontend:
                    let errorMessage = 'Ein Fehler ist aufgetreten. Bitte versuche es später noch einmal.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;  // Falls der Server eine Fehlermeldung zurückgibt
                    }
                    alert(errorMessage);  // Zeige eine allgemeine Fehlermeldung an
                }
            });
            };
        }
        function deleteShift(e){
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });
            $.ajax({
                url: `/scheduling/deleteShift/${e.target.dataset.shiftid}`,
                type: 'DELETE',
                success: function(data) {
                    updateWeek();
                    
                },
                error: function(xhr, status, error) {
                    // Fehlerbehandlung: Logge die Antwort und zeige sie in der Konsole
                    alert("error");
                    console.error('Fehler bei der Anfrage:', error);
                    console.error('Status:', status);
                    console.error('Antwort:', xhr.responseText);
                    
                    // Optionale Ausgabe einer Fehlernachricht im Frontend:
                    let errorMessage = 'Ein Fehler ist aufgetreten. Bitte versuche es später noch einmal.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;  // Falls der Server eine Fehlermeldung zurückgibt
                    }
                    alert(errorMessage);  // Zeige eine allgemeine Fehlermeldung an
                }
            });
            
        }
        function usersListEqualHeight(){
            let maxHeight = 0;
            let currentElementHeight = 0;
            let usersList = document.querySelectorAll(".user-list");
            for(i = 0; i < usersList.length; i++){
                currentElementHeight  = usersList[i].offsetHeight ;
                if(currentElementHeight > maxHeight){
                    maxHeight = currentElementHeight;
                }
            }
            for(i = 0; i < usersList.length;i++){
                usersList[i].style = "height: " + maxHeight + "px";
            }
        }
        function amaountEmployeesEqualHeight(){
            let maxHeight = 0;
            let currentElementHeight = 0;
            let employeesList = document.querySelectorAll(".list-employees");
            for(i = 0; i < employeesList.length; i++){
                currentElementHeight  = employeesList[i].offsetHeight ;
                if(currentElementHeight > maxHeight){
                    maxHeight = currentElementHeight;
                }
            }
            for(i = 0; i < employeesList.length;i++){
                employeesList[i].style = "height: " + maxHeight + "px";
            }

        }

        function showUnfilledShifts(){
            const regex = /(\d+)\/(\d+)/;
            let all_shifts_employees = document.querySelectorAll('.list-employees');
            let first_value;
            let second_value;
            let splitted;
            for(i=0;i < all_shifts_employees.length;i++){
                splitted = all_shifts_employees[i].textContent.match(regex)[0].split("/");
                first_value = splitted[0];
                second_value = splitted[1];
                if(first_value !== second_value){
                    all_shifts_employees[i].closest(".list-group").style = "background-color: " + "#D6DBD4;"
                }
            }
        }

    </script>
    <style>
        .list-group {
            margin-bottom: 20px;
            padding: 10px;
        }
        .td-wrapper-flex {
            display: flex;
            align-items: center;
            flex-direction: column;
        }
        #modal-error-message-2 {
            color: red;
        }
        .second-modal-employees-workingHours-label {
            display: flex;
            justify-content: space-between;
        }
        

    </style>
@endsection