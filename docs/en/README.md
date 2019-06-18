# IPSymconWeatherStation
[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Symcon%20Version-5.0%20%3E-green.svg)](https://www.symcon.de/forum/threads/38222-IP-Symcon-5-0-verf%C3%BCgbar)


Module for IP-Symcon from version 5.x. Allows to receive data from a weather station (Sainlogic / Froggit / ELV) with WLAN.

## Documentation

**Table of Contents**

1. [Features](#1-features)
2. [Requirements](#2-requirements)
3. [Installation](#3-installation)
4. [Function reference](#4-functionreference)
5. [Configuration](#5-configuration)
6. [Annex](#6-annex)

## 1. Features

The module displays the data of a Sainlogic / Froggit / ELV weather station in IP-Symcon.

### Receive data from a Sainlogic / Froggit / ELV weather station:

  - Receive the data of the weather station
  - optionally upload the data to Wunderground

## 2. Requirements

 - IP-Symcon 5.x
 - Sainlogic (from Firmware 1.3.8) / Froggit / ELV Wetterstation
 - the Master Branch is designed for the current IP-Symcon version.
 - For IP-Symcon versions smaller than 5.1 the branch _Old-Version_ has to be selected

## 3. Installation

### a. Setup of the weather station

First, the weather station is to be put into operation.
For a Sainlogic weather station at least firmware version 1.3.8 must be available. In a Sainlogic weather station additional settings have to be made e.g. with the app _WS View_.
![server](img/custom_server.png?raw=true "server")

Here you have to enter the IP address of Symcon at _Hostname_.
_Station ID_ and _Station Key_ are freely selectable, but a value must be entered here. _Port_ is then set in IP-Symcon, default settings is _45000_.
_Wunderground_ is to be selected as _Protocol Type_.


### b. Connect with IFTTT and Wunderground (optional)

### c. Loading the module

Open the IP Console's web console with _http://<IP-Symcon IP>:3777/console/_.

Then click on the module store icon (IP-Symcon > 5.1) in the upper right corner.

![Store](img/store_icon.png?raw=true "open store")

In the search field type

```
Wetterstation
```  


![Store](img/module_store_search_en.png?raw=true "module search")

Then select the module and click _Install_

![Store](img/install_en.png?raw=true "install")


#### Install alternative via Modules instance (IP-Symcon < 5.1)

Open the IP Console's web console with _http://<IP-Symcon IP>:3777/console/_.

_Open_ the object tree .

![Objektbaum](img/object_tree.png?raw=true "Objektbaum")	

Open the instance _'Modules'_ below core instances in the object tree of IP-Symcon (>= Ver 5.x) with a double-click and press the _Plus_ button.

![Modules](img/modules.png?raw=true "Modules")	

![Plus](img/plus.png?raw=true "Plus")	

![ModulURL](img/add_module.png?raw=true "Add Module")
 
Enter the following URL in the field and confirm with _OK_:

```
https://github.com/Wolbolar/IPSymconWeatherStation
```  
	         
Then an entry for the module appears in the list of the instance _Modules_

By default, the branch _master_ is loaded, which contains current changes and adjustments.
Only the _master_ branch is kept current.

![Master](img/master.png?raw=true "master") 

If an older version of IP-Symcon smaller than version 5.1 is used, click on the gear on the right side of the list.
It opens another window,

![SelectBranch](img/select_branch_en.png?raw=true "select branch") 

here you can switch to another branch, for older versions smaller than 5.1 select _Old-Version_ .

### d. Configuration in IP-Symcon

In IP-Symcon add _Instance_ (_CTRL + 1_) under the category under which you want to add the weather station and select _WeatherStation_.
In the configuration form the MAC address of the weather station has to be added.


## 4. Function reference

### Weatherstation:


## 5. Configuration:

### Weather Station:

| Eigenschaft | Typ     | Standardwert | Funktion                                  |
| :---------: | :-----: | :----------: | :---------------------------------------: |
| MAC         | string  |              | MAC Adresse Wetterstation                 |






## 6. Annnex

###  a. Functions:

#### Wetter Station:

`WeatherStation_FindStation(integer $InstanceID)`

Search the weatherstation in the network und returns the ip adress


###  b. GUIDs and data exchange:

#### WeatherStation:

GUID: `{FBDB2770-0232-43D2-F40B-1240CEAF6CD4}` 