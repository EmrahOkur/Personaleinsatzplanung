
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Schichten planen') }}
            <div class="month-navigation">
                <button id="prev-week" class="btn btn-primary">Vorherige Woche</button>
                <span id="week-label" class="mx-2"></span>
                <button id="next-week" class="btn btn-primary">Nächste Woche</button>
            </div>
        </h2>
    </x-slot>
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
                            @if (count($users) > 0)
                            <input type="number" id="employee_schedule" name="employee_schedule" class="form-control" value="1" min="1" max="{{count($users)}}">
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
    
    <script>

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
                const dateElement = document.createElement('span');
                const addshiftButton = document.createElement('button');
                addshiftButton.textContent = "Schicht hinzufügen";
                addshiftButton.setAttribute("class", "btn btn-primary padding-5px");
                addshiftButton.setAttribute("data-bs-target", "#scheduleModal"); 
                addshiftButton.setAttribute("data-bs-toggle", "modal");
                addshiftButton.setAttribute("data-date", `${dateCounter.getDate()}.${dateCounter.getMonth() + 1}.${dateCounter.getFullYear()}`);
                addshiftButton.setAttribute("onclick", "firstScheduleModalDate(event)");
                dateElement.textContent = `${dateCounter.getDate()}.${dateCounter.getMonth() + 1}.${dateCounter.getFullYear()}`;

                // Schicht wird für den Tag angezeigt, falls vorhanden
                let shiftsForDay = data.filter(shift => shift.date_shift === dateElement.textContent);

                shiftsForDay.forEach(shift => {
                    const shiftDiv = document.createElement('div');
                    shiftDiv.classList.add('list-group');
                    let users_arr = [];
                    shift.users.forEach(user => {
                        users_arr.push(user);
                    })
                    shiftDiv.innerHTML = `
                        <p class="list-group-item">Start: ${shift.start_time}</p>
                        <p class="list-group-item">Ende: ${shift.end_time}</p>
                        <p class="list-group-item">Mitarbeiter: ${users_arr.length}/${shift.amount_employees}</p>
                         <p> ${users_arr ? users_arr.map(user => user.name).join(', ') : "Keine Mitarbeiter zugewiesen"} </p>
                        <button class="btn btn-success" onclick="secondScheduleModal(event)" data-shiftid = ${shift.id} data-requiredemployees = ${shift.amount_employees} data-bs-target="#secondModalSchedule" id="secondModalAddEmployees" data-bs-toggle="modal">Schicht bearbeiten </button>
                    `;
                    tddiv.appendChild(shiftDiv);
                });

                tddiv.setAttribute("class", "shift-list");
                tddiv.setAttribute("id", dateElement.textContent);

                // Füge das Datum und den Button zur Zelle hinzu
                dayCell.appendChild(dateElement);
                dayCell.appendChild(tddiv);
                dayCell.appendChild(addshiftButton);

                row.appendChild(dayCell);

                // Gehe zum nächsten Tag
                dateCounter.setDate(dateCounter.getDate() + 1);
            }

            // Füge die Zeile der Tabelle hinzu
            scheduleBody.appendChild(row);

            document.querySelectorAll('#secondModalAddEmployees').forEach(button =>{
                
            button.addEventListener('click', function(){
            //    console.log("secondModalAddEmployees");
                //resetcheckboxes();
            });
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
        fetch(`/scheduling/getEmployeesForShift/${shiftId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(user => {
                    let checkboxDiv = document.createElement('div');
                    checkboxDiv.classList.add('form-check');
                    // Vielleicht korrigieren
                    checkboxDiv.innerHTML = `
                        <input class="form-check-input" name="employee_ids[]" type="checkbox" value="${user.id}" id="employee_${user.id}">
                        <label class="form-check-label" for="employee_${user.id}">
                            ${user.name}
                        </label>
                    `;
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
                console.log(["320","320","320"])
            });

            // Mitarbeiter zuweisen oder entfernen
            fetch('/scheduling/assignEmployeesToShift', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    shift_id: shiftId,
                    employee_ids: selectedEmployees
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log("test");
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
            let date = document.getElementById("schedule-date").textContent;
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });
            $.ajax({
                url: `/scheduling/addshifts`,
                type: 'POST',
                data:{start_time:start_time, end_time:end_time, amount_employees:amount_employees, date:date },
                success: function(data) {
                    let shift_list = document.getElementById(data.date_shift);
                    let created_shift = document.createElement('div');
                    created_shift.setAttribute("class","list-group");
                    shift_list.appendChild(created_shift);
                    created_shift.innerHTML = `<p class="list-group-item">Start: ${data.start_time} </p> <p class="list-group-item" >Ende: ${data.end_time} </p> <div class="list-group-item">Mitarbeiter: 0/${data.amount_employees} </div> <button class="btn btn-success" onclick="secondScheduleModal(event)" data-shiftid = ${data.id} data-requiredemployees = ${data.amount_employees} data-bs-target="#secondModalSchedule" id="secondModalAddEmployees" data-bs-toggle="modal">Schicht bearbeiten </button>`;
                    
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
            console.log(`/scheduling/deleteShift/${e.target.dataset.shiftid}`);
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

    </script>
    <style>
        .list-group {
            margin-bottom: 20px;
        }
        .padding-5px {
            
        }
    </style>
</x-app-layout>