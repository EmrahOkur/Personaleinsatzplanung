<?php

namespace App\Http\Controllers;
use App\Models\Urlaub_Eintrag;
use App\Models\Urlaub;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UrlaubController extends Controller
{
    


    public function index()
    {// Rufe alle Einträge mit Abwesenheitsart "Urlaub" ab
    $urlaubs = DB::table('urlaubs')
        ->select('datum_start', 'datum_ende', 'abwesenheitsart')
        ->where('abwesenheitsart', 'Urlaub')
        ->get();

    // Initialisierung von Werten

    $verplante_tage = 0;
    $genommene_tage = 0;
    $heute = new DateTime();

    foreach ($urlaubs as $urlaub) {
        $startDatum = new DateTime($urlaub->datum_start);
        $endDatum = new DateTime($urlaub->datum_ende);

        // Berechne die Differenz in Tagen (+1 um den Endtag einzuschließen)
        $difference = $startDatum->diff($endDatum)->days + 1;

        if ($endDatum < $heute) {
            // Liegt in der Vergangenheit: `genommene_tage`
            $genommene_tage += $difference;
        } elseif ($startDatum > $heute) {
            // Liegt in der Zukunft: `verplante_tage`
            $verplante_tage += $difference;
        }
    }

    // Rufe die verbleibenden Tage aus der Datenbank ab
    $verfügbare_tage = (30);
    $verbleibende_tage = $verfügbare_tage - $genommene_tage - $verplante_tage;

    DB::table('urlaubs')->update(['verbleibende_tage' => $verbleibende_tage]);
    $urlaubs = Urlaub::query()->get();
    
    $urlaubs = Urlaub::all();

    // Daten an die Ansicht übergeben
    return view('urlaub', compact('verfügbare_tage', 'genommene_tage', 'verplante_tage', 'verbleibende_tage', 'urlaubs'));
}
    public function beantragen()
    {
        $urlaubs = Urlaub::all();

         $verbleibende_tage =  DB::table('urlaubs')->value('verbleibende_tage');
         $genehmigender = 'Vorgesetzte';
         DB::table('urlaubs')->update(['genehmigender' => $genehmigender]);
        
            
        return view('urlaub.beantragen', compact('verbleibende_tage','genehmigender'));
    }

    public function destroy($id)
{
    // Urlaub anhand der ID finden
    $urlaub = Urlaub::find($id);

    if (!$urlaub) {
        return redirect()->back()->with('error', 'Eintrag nicht gefunden.');
    }

    // Urlaub löschen
    $urlaub->delete();
   
    // Erfolgreiche Rückmeldung
    return redirect()->route('urlaubs')->with('success', 'Eintrag wurde erfolgreich gelöscht.');
}
    public function übersicht()
    {
        $urlaubs = DB::table('urlaubs')
        ->select('selectedDates')
        ->where('abwesenheitsart', 'Urlaub')
        ->get();

    // Konvertiere die `selectedDates`-Einträge in ein Array
    $events = [];
    foreach ($urlaubs as $urlaub) {
        $dates = json_decode($urlaub->selectedDates, true);
        foreach ($dates as $date) {
            $events[] = [
                'title' => 'Urlaub', // Beschriftung des Events
                'start' => $date,
                'allDay' => true, // Ganztägiges Event
            ];
        }
    }

    return view('urlaub.übersicht', compact('events'));
    }



    public function speichern(Request $request)
    {
        // Schritt 1: Die ausgewählten Daten aus der Anfrage abrufen und in ein Array umwandeln
        $selectedDates = json_decode($request->input('selectedDates'), true);
        $resturlaubstage = DB::table('urlaubs')->value('verbleibende_tage');
        if (count($selectedDates) > $resturlaubstage) {
            return redirect()->back()->withErrors(['selectedDates' => 'Die Anzahl der ausgewählten Tage darf nicht größer sein als die Resturlaubstage.']);
    
        // Überprüfen, ob die Umwandlung erfolgreich war
        if (!is_array($selectedDates)) {
            return back()->withErrors(['selectedDates' => 'Ungültiges Format für ausgewählte Daten.']);

             // Anzahl der ausgewählten Tage
       
    }

        }
    
        // Den aktuellen Tag abrufen
        $heute = new DateTime();
    
        // Schritt 2: Den Datumsbereich verarbeiten
        $dateRanges = $this->processSelectedDates($selectedDates);
    
        // Schritt 3: Validierung der Startdaten
        foreach ($dateRanges as $range) {
            $startDatum = new DateTime($range['start']);
            $abwesenheitsart = $request->input('abwesenheitsart');
    
            if ($abwesenheitsart === 'Urlaub') {
                // Prüfen, ob der Starttag größer als heute ist
                if ($startDatum <= $heute) {
                    return back()->withErrors(['selectedDates' => 'Das Startdatum muss für die Abwesenheitsart "Urlaub" in der Zukunft liegen.']);
                }
            } else {
                // Prüfen, ob der Starttag heute oder in der Zukunft ist
                if ($startDatum < $heute) {
                    return back()->withErrors(['selectedDates' => 'Das Startdatum darf für diese Abwesenheitsart darf nicht in der Vergangenheit liegen.']);
                }
            }
            
        }
    
        // Schritt 4: Speichern der Bereiche in der Datenbank und die Tage als JSON-String speichern
        foreach ($dateRanges as $range) {
            Urlaub::create([
                'datum_start' => $range['start'],
                'datum_ende' => $range['end'],
                'abwesenheitsart' => $request->input('abwesenheitsart'),
                'genehmigender' => $request->input('genehmigender'),
                'zusatzinfo' => $request->input('zusatzinfo'),
                'selectedDates' => json_encode($selectedDates),
                'status' => 'informiert',   // Die ausgewählten Tage als JSON-String speichern
                // andere Felder nach Bedarf...
            ]);
        }
    
        return redirect()->route('urlaubs');
    }
    
    /**
     * Die Funktion zur Verarbeitung der ausgewählten Daten.
     */
    private function processSelectedDates(array $dates): array
    {
        // Sortiere das Array der Daten
        sort($dates);
    
        $result = [];
        $currentRange = ['start' => $dates[0], 'end' => $dates[0]];
    
        for ($i = 1; $i < count($dates); $i++) {
            $previousDate = new DateTime($dates[$i - 1]);
            $currentDate = new DateTime($dates[$i]);
    
            // Prüfen, ob der Unterschied zwischen den beiden Tagen genau ein Tag beträgt
            $difference = $previousDate->diff($currentDate)->days;
    
            if ($difference === 1) {
                // Die Tage sind aufeinanderfolgend
                $currentRange['end'] = $dates[$i];
            } else {
                // Eine Unterbrechung wurde gefunden
                $result[] = $currentRange;  // Speichern des aktuellen Bereichs
                $currentRange = ['start' => $dates[$i], 'end' => $dates[$i]]; // Neuer Bereich
            }
        }
    
        // Füge den letzten Bereich hinzu
        $result[] = $currentRange;
    
        return $result;
    }

    

    public function feiertage()
    {
        $bundesland = 'HH';
        $jahr = date('Y');

        $url = "https://feiertage-api.de/api/?jahr={$jahr}&nur_land={$bundesland}";
        $response = Http::get($url);

        if ($response->ok()) {
            $feiertage = $response->json();
            return response()->json($feiertage);
        } else {
            return response()->json(['error' => 'Feiertage konnten nicht geladen werden.'], 500);
        }
   }

   
}