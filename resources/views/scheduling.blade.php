
@extends('layouts.app')
@section('header')
    <section class="header d-flex flex-column align-items-center" id="schedule-header" data-loggeduserid = "{{Auth::id()}}" >
        <h2 class="font-semibold text-xl text-gray-800 leading-tight" id="schedule-h2">

        </h2>
        <div class="month-navigation">
            <button id="prev-week" class="btn btn-primary">Vorherige Woche</button>
            <span id="week-label" class="mx-2"></span>
            <button id="next-week" class="btn btn-primary">Nächste Woche</button>
        </div>
    </section>
@endsection
@section('main')
    <!-- 2. Modalfenster -->
    <div class="modal fade" id="secondModalSchedule" tabindex="-1" aria-labelledby="secondModalScheduleLabel" aria-hidden="true">
    <div class="modal-dialog">
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
                    <div class="row">
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
        </div>
    </div>
    
    <script>
        let userId = document.getElementById("schedule-header").dataset.loggeduserid;

        function formateDate(laravelDate){
            let formattedDate = moment(laravelDate).format('DD.MM.YYYY');
            console.log(formattedDate);  
            return formattedDate;
        }
        function loadEmployeesFromDepartment(){
            fetch(`/departments/getEmployeesFromDepartmentByUser/${userId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("schedule-h2").textContent = "Abteilung: " + data.department.name;
                    console.log("data.departmentEmployees ",data.departmentEmployees)
                    let departmentEmployees = data.departmentEmployees;
                    let sidebar = document.getElementById('schedule-employee-sidebar');
                    let sidebarEmployees;
                    departmentEmployees.forEach(employee => {
                        sidebarEmployees = document.createElement("p");
                        sidebarEmployees.innerHTML = employee.first_name + " " + employee.last_name;
                        sidebar.appendChild(sidebarEmployees);
                    });

                    
                })
        }
        loadEmployeesFromDepartment();
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

            // Schichten werden geladen
            fetch('/scheduling/getShifts')
                .then(response => response.json())
                .then(data => {
            

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
                addshiftButton.setAttribute("class", "btn btn-primary padding-5px");
                addshiftButton.setAttribute("data-bs-target", "#scheduleModal"); 
                addshiftButton.setAttribute("data-bs-toggle", "modal");
                addshiftButton.setAttribute("data-date", `${dateCounter.getDate()}.${dateCounter.getMonth() + 1}.${dateCounter.getFullYear()}`);
                addshiftButton.setAttribute("onclick", "firstScheduleModalDate(event)");
                dateElement.textContent = `${dateCounter.getDate()}.${dateCounter.getMonth() + 1}.${dateCounter.getFullYear()}`;

                // Schicht wird für den Tag angezeigt, falls vorhanden
                let shiftsForDay = data.filter(shift => formateDate(shift.date_shift) === dateElement.textContent);

                shiftsForDay.forEach(shift => {
                    const shiftDiv = document.createElement('div');
                    shiftDiv.classList.add('list-group');
                    let employees_arr = [];
                    // Alle Mitarbeiter der zugeordneten Schicht werden gezählt
                    shift.employees.forEach(employee => {
                        employees_arr.push(employee);
                    })
                    shiftDiv.innerHTML = `
                        <p class="list-group-item">${shift.start_time} - ${shift.end_time}</p>
                        <p class="list-group-item list-employees">Mitarbeiter: ${employees_arr.length}/${shift.amount_employees}</p>
                         <p class="user-list"> ${employees_arr ? employees_arr.map(employee => employee.first_name +" " + employee.last_name).join(', ') : "Keine Mitarbeiter zugewiesen"} </p>
                        <button class="btn btn-success" onclick="secondScheduleModal(event)" data-shiftid = ${shift.id} data-requiredemployees = ${shift.amount_employees} data-bs-target="#secondModalSchedule" id="secondModalAddEmployees" data-bs-toggle="modal">Bearbeiten </button>
                    `;
                    tddiv.appendChild(shiftDiv);
                });

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

            document.querySelectorAll('#secondModalAddEmployees').forEach(button =>{
                
            usersListEqualHeight();
            amaountEmployeesEqualHeight();
            showUnfilledShifts();
        })

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

        // Lade Mitarbeiter für diese Schicht
        console.log(`/scheduling/getEmployeesForShift/${shiftId}/${userId}`)
        fetch(`/scheduling/getEmployeesForShift/${shiftId}/${userId}`)
            .then(response => response.json())
            .then(data => {
                data.employees.forEach(employee => {
                    console.log("employees ",employee)
                    let checkboxDiv = document.createElement('div');
                    checkboxDiv.classList.add('form-check');
                    console.log("employeesInShift ",data.employeesInShift)
                    console.log("the employee ",employee)
                    // Falls user in der Schicht enthalten ist
                    if(data.employeesInShift.includes(employee.id) ){
                        checkboxDiv.innerHTML =
                        `
                        <input class="form-check-input checked" name="employee_ids[]" type="checkbox" value="${employee.id}" id="employee_${employee.id}" checked="true">
                        <label class="form-check-label" for="employee_${employee.id}">
                            ${employee.first_name} - ${employee.last_name}
                        </label>
                    `;
                    }else{
                        console.log("not checked")
                    checkboxDiv.innerHTML = `
                        <input class="form-check-input" name="employee_ids[]" type="checkbox" value="${employee.id}" id="employee_${employee.id}">
                        <label class="form-check-label" for="employee_${employee.id}">
                            ${employee.first_name}  ${employee.last_name}
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
                console.log(selectedEmployees)
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
            let start_time = document.getElementById("start_shift").value;
            let end_time = document.getElementById("end_shift").value;
            let amount_employees = document.getElementById("employee_schedule").value;
            console.log('amount_employees '+amount_employees);
            console.log('amount_employees '+ document.querySelector('[name="_token"]').getAttribute('content'));
            let date = document.getElementById("schedule-date").textContent;
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //'X-CSRF-TOKEN': document.querySelector('[name="_token"]').getAttribute('content')
            }
            });
            $.ajax({
                url: `/scheduling/addshifts`,
                type: 'POST',
                data:{start_time:start_time, end_time:end_time, amount_employees:amount_employees, date:date },
                success: function(data) {
                    console.log("date ",data.date_shift)
                    let formattedDate = formateDate(data.date_shift);
                    let shift_list = document.getElementById(formattedDate);
                    let created_shift = document.createElement('div');
                    created_shift.setAttribute("class","list-group");
                    shift_list.appendChild(created_shift);
                    created_shift.innerHTML = `<p class="list-group-item">${data.start_time} - ${data.end_time} </p> <p class="list-group-item list-employees"> Mitarbeiter: 0/${data.amount_employees} </p> <p class="user-list">  </p> <button class="btn btn-success" onclick="secondScheduleModal(event)" data-shiftid = ${data.id} data-requiredemployees = ${data.amount_employees} data-bs-target="#secondModalSchedule" id="secondModalAddEmployees" data-bs-toggle="modal">Bearbeiten </button>`;
                    usersListEqualHeight();
                    amaountEmployeesEqualHeight();
                    showUnfilledShifts();
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
        

    </style>
@endsection