# IPSymconWeatherStation

Modul für IP-Symcon ab Version 4. Ermöglicht das Empfangen von Daten einer Wetterstation (Ambient oder Sainlogic) mit WLAN.

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)  
2. [Voraussetzungen](#2-voraussetzungen)  
3. [Installation](#3-installation)  
4. [Funktionsreferenz](#4-funktionsreferenz)
5. [Konfiguration](#5-konfiguartion)  
6. [Anhang](#6-anhang)  

## 1. Funktionsumfang

Mit dem Modul lassen sich die Daten einer Sainlogic / Ambient Wetterstation abholen. 

### Daten einer Sainlogic oder Ambient Wetterstation abholen:  

 - Empfangen der Daten der Wetterstation 
 - optional hochladen der Daten zu Wunderground
  

## 2. Voraussetzungen

 - IP-Symcon 4.x


## 3. Installation

### a. Einrichtung der Wetterstation

Zunächst ist die Wetterstation in Betrieb zu nehmen. Dabei ist dann die MAC Adresse der Wetterstation zu notieren.


### b. Koppeln an IFTTT und Wunderground (Optional)



### c. Laden des Moduls


Die IP-Symcon (min Ver. 4.x) Konsole öffnen. Im Objektbaum unter Kerninstanzen die Instanz __*Modules*__ durch einen doppelten Mausklick öffnen.

![Modules](docs/Modules.png?raw=true "Modules")

In der _Modules_ Instanz rechts oben auf den Button __*Hinzufügen*__ drücken.

![Modules](docs/Hinzufuegen.png?raw=true "Hinzufügen")
 
In dem sich öffnenden Fenster folgende URL hinzufügen:

	
    `https://github.com/Wolbolar/IPSymconWeatherStation`  
    
und mit _OK_ bestätigen.    
 

### d. Einrichtung in IPS

In IP-Symcon nun _Instanz hinzufügen_ (_CTRL+1_) auswählen unter der Kategorie, unter der man die Wetterstation hinzufügen will, und _WeatherStation_ auswählen.
Im Konfigurationsformular ist die MAC Adresse der Wetterstation zu ergänzen.

## 4. Funktionsreferenz

### Wetter Station:


## 5. Konfiguration:

### Wetter Station:

| Eigenschaft | Typ     | Standardwert | Funktion                                  |
| :---------: | :-----: | :----------: | :---------------------------------------: |
| MAC         | string  |              | MAC Adresse Wetterstation                 |


## 6. Anhang

###  a. Funktionen:

#### Wetter Station:

`WeatherStation_GetData(integer $InstanceID)`

Holt die Daten der Wetterstation ab


###  b. GUIDs und Datenaustausch:

#### WeatherStation:

GUID: `{FBDB2770-0232-43D2-F40B-1240CEAF6CD4}` 




