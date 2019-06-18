# IPSymconWeatherStation
[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Symcon%20Version-5.0%20%3E-green.svg)](https://www.symcon.de/forum/threads/38222-IP-Symcon-5-0-verf%C3%BCgbar)


Modul für IP-Symcon ab Version 5.x. Ermöglicht das Empfangen von Daten einer Wetterstation (Sainlogic / Froggit / ELV) mit WLAN.

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)  
2. [Voraussetzungen](#2-voraussetzungen)  
3. [Installation](#3-installation)  
4. [Funktionsreferenz](#4-funktionsreferenz)
5. [Konfiguration](#5-konfiguartion)  
6. [Anhang](#6-anhang)  

## 1. Funktionsumfang

Mit dem Modul lassen sich die Daten einer Sainlogic / Froggit / ELV Wetterstation in IP-Symcon anzeigen.  

### Daten einer Sainlogic / Froggit / ELV Wetterstation empfangen:  

 - Empfangen der Daten der Wetterstation 
 - optional hochladen der Daten zu Wunderground
  

## 2. Voraussetzungen

 - IP-Symcon 5.x
 - Sainlogic (ab Firmware 1.3.8) / Froggit / ELV Wetterstation
 - der Master Branch ist für die aktuelle IP-Symcon Version ausgelegt.
 - bei IP-Symcon Versionen kleiner 5.1 ist der Branch _5.0_ zu wählen

## 3. Installation

### a. Einrichtung der Wetterstation

Zunächst ist die Wetterstation in Betrieb zu nehmen. 
Bei einer Sainlogic Wetterstation muss mindestens die Firmware Version 1.3.8 vorhanden sein. Bei einer Sainlogic Wetterstation sind zusätzlich Einstellungen vorzunehmen z.B. mit der App _WS View_.

![server](img/custom_server.png?raw=true "server")

Hier ist bei _Hostname_ die IP Adresse von Symcon einzutragen.
_Station ID_ und _Station Key_ sind frei wählbar, hier muss aber ein Wert eingetragen werden. _Port_ wird dann in IP-Symcon eingestellt, Standard Einstellungen ist _45000_.
Als _Protocol Type_ ist _Wunderground_ auszuwählen. 


### b. Koppeln an IFTTT und Wunderground (Optional)

### c. Laden des Moduls

Die Webconsole von IP-Symcon mit _http://<IP-Symcon IP>:3777/console/_ öffnen. 


Anschließend oben rechts auf das Symbol für den Modulstore (IP-Symcon > 5.1) klicken

![Store](img/store_icon.png?raw=true "open store")

Im Suchfeld nun

```
Wetterstation
```  

eingeben

![Store](img/module_store_search.png?raw=true "module search")

und schließend das Modul auswählen und auf _Installieren_

![Store](img/install.png?raw=true "install")

drücken.


#### Alternatives Installieren über Modules Instanz (IP-Symcon < 5.1)

Die Webconsole von IP-Symcon mit _http://<IP-Symcon IP>:3777/console/_ öffnen. 

Anschließend den Objektbaum _Öffnen_.

![Objektbaum](img/objektbaum.png?raw=true "Objektbaum")	

Die Instanz _'Modules'_ unterhalb von Kerninstanzen im Objektbaum von IP-Symcon (>=Ver. 5.x) mit einem Doppelklick öffnen und das  _Plus_ Zeichen drücken.

![Modules](img/Modules.png?raw=true "Modules")	

![Plus](img/plus.png?raw=true "Plus")	

![ModulURL](img/add_module.png?raw=true "Add Module")
 
Im Feld die folgende URL eintragen und mit _OK_ bestätigen:

```
https://github.com/Wolbolar/IPSymconWeatherStation
```  
	        
Anschließend erscheint ein Eintrag für das Modul in der Liste der Instanz _Modules_    

Es wird im Standard der Zweig (Branch) _master_ geladen, dieser enthält aktuelle Änderungen und Anpassungen.
Nur der Zweig _master_ wird aktuell gehalten.

![Master](img/master.png?raw=true "master") 

Sollte eine ältere Version von IP-Symcon die kleiner ist als Version 5.1 eingesetzt werden, ist auf das Zahnrad rechts in der Liste zu klicken.
Es öffnet sich ein weiteres Fenster,

![SelectBranch](img/select_branch.png?raw=true "select branch") 

hier kann man auf einen anderen Zweig wechseln, für ältere Versionen kleiner als 5.1 ist hier
_Old-Version_ auszuwählen. 

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