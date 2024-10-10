# Personaleinsatzplanung

## Installation
Ihr benötigt für diese Anleitung folgendes:

- Windows 10 oder 11
- WSL 2
- Docker Desktop
- Visual Studio Code
- Die "Remote - Containers" Erweiterung für VS Code

Hinweise zur Installation von Docker-Desktop
- Benutzereinrichtung erforderlich
- Einstellungen -> Resources: Haken setzen bei "Enable integration with my default WSL distro" + Haken setzen bei "Ubuntu"

## Folgende Punkte im WSL abarbeiten

#### SSH-Key
SSH-Key erstellen im Order ~/.shh
Den dabei entstehenden public-key müsst ihr ins [Github](https://github.com/settings/keys) kopieren.
Wir brauchen ssh um aus dem Container das Github-Repository zu erreichen.

evtl. Order "workspace" erstellen und öffnen 
```mkdir workspace && cd workspace``

Falls git nivt installiert sein sollte: ```sudo apt install git```

Repository clonen mit
```git clone git@github.com:verimich/Personaleinsatzplanung.git```
-> Projekt wird in Verzeichnis Personaleinsatzplanung geklont

In das Projekt-Verzeichnis wechseln und ausführen:
```docker-composer up -d build``
-> damit erstellen wir uns einen Datenbank Container

Projekt in VS Code öffen im Repo-Verzeichnis mit ```code .```

Im VS Code -> "F1" -> Dev Container: Build Container ausführen -> das dauert etwas

## Folgende Punkte im Dev-Container:

#### Pakete installieren
Für PHP
```composer install```

Node-Pakete installieren mit
```npm install```

#### Datenbank erstellen
!Achtung! Bestehende Daten werden überschrieben
```php artisan migrate:fresh```

#### PHP-Server starten mit
```php artisan serve```
und in einem zweiten Terminalfenster 
```npm run dev```