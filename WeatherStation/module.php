<?php

declare(strict_types=1);

class WeatherStation extends IPSModule
{
    // helper properties
    private $position = 0;

    private const Celsius          = 1; // Celsius °C
    private const Fahrenheit       = 2; // Fahrenheit F
    private const kmh              = 1; // kmh
    private const mph              = 2; // mph
    private const pascal           = 1; // pascal
    private const bar              = 2; // bar
    private const mm               = 1; // mm
    private const inch             = 2; // inch
    private const Sainlogic        = 1; // Sainlogic
    private const ELV_WS980WiFi    = 2; // ELV WS980WiFi
    private const Froggit_WH4000SE = 3; // Froggit WH4000SE

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        //These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.

        $this->RequireParent('{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}');

        $this->RegisterPropertyString('Wunderground_Station_ID', '');
        $this->RegisterPropertyString('Wunderground_Station_Password', '');
        $this->RegisterPropertyString('Weathercloud_ID', '');
        $this->RegisterPropertyString('Weathercloud_Key', '');
        $this->RegisterPropertyString('ApiKey', '');
        $this->RegisterPropertyString('ApplicationKey', 'ac14c2f0d58541d0a4714e51d97785e95728ce6ade5743dc8bec238fcc2c715b'); // API_Development_Key
        $this->RegisterPropertyString('MAC', '');
        $this->RegisterPropertyString('Ambient_Passkey', '');
        $this->RegisterPropertyInteger('temp_unit', 1);
        $this->RegisterPropertyInteger('speed_unit', 1);
        $this->RegisterPropertyInteger('pressure_unit', 1);
        $this->RegisterPropertyInteger('rain_unit', 1);
        $this->RegisterPropertyInteger('altitude_above_sea_level', 0);
        $this->RegisterPropertyInteger('model', 0);
        $this->RegisterPropertyString('weatherstation_info', '[]');
        $this->RegisterAttributeString('weatherstation_name', '');
        $this->RegisterAttributeString('weatherstation_mac', '');
        $this->RegisterAttributeString('weatherstation_address', '');
        $this->RegisterAttributeInteger('weatherstation_port', 0);

        $this->RegisterPropertyInteger('UpdateInterval_Wunderground', 20);
        $this->RegisterTimer('WundergroundTimerUpdate', 0, 'WeatherStation_Update_Wunderground(' . $this->InstanceID . ');');

        $this->RegisterPropertyInteger('UpdateInterval_Weathercloud', 10);
        $this->RegisterTimer('WeathercloudTimerUpdate', 0, 'WeatherStation_Update_Weathercloud(' . $this->InstanceID . ');');

        $this->RegisterPropertyInteger('UpdateInterval_Weatherbug', 10);
        $this->RegisterTimer('WeatherbugTimerUpdate', 0, 'WeatherStation_Update_Weatherbug(' . $this->InstanceID . ');');

        $this->RegisterPropertyInteger('UpdateInterval_AmbientWeather', 10);
        $this->RegisterTimer('AmbientWeatherTimerUpdate', 0, 'WeatherStation_Update_AmbientWeatherCloud(' . $this->InstanceID . ');');

        $this->RegisterPropertyInteger('UpdateInterval_Data', 10);
        $this->RegisterTimer('UpdateData', 0, 'WeatherStation_GetData(' . $this->InstanceID . ');');
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        $temp_unit     = $this->ReadPropertyInteger('temp_unit');
        $speed_unit    = $this->ReadPropertyInteger('speed_unit');
        $pressure_unit = $this->ReadPropertyInteger('pressure_unit');

        if ($temp_unit == self::Celsius) {
            $this->RegisterVariableFloat('Indoor_Temp', $this->Translate('Indoor Temperature'), '~Temperature', $this->_getPosition());
            $this->RegisterVariableFloat('Outdoor_Temp', $this->Translate('Outdoor Temperature'), '~Temperature', $this->_getPosition());
            $this->RegisterVariableFloat('Windchill', $this->Translate('Windchill'), '~Temperature', $this->_getPosition());
            $this->RegisterVariableFloat('Dewpoint', $this->Translate('Dewpoint'), '~Temperature', $this->_getPosition());
        } else {
            $this->RegisterVariableFloat('Indoor_Temp', $this->Translate('Indoor Temperature'), '~Temperature.Fahrenheit', $this->_getPosition());
            $this->RegisterVariableFloat('Outdoor_Temp', $this->Translate('Outdoor Temperature'), '~Temperature.Fahrenheit', $this->_getPosition());
            $this->RegisterVariableFloat('Windchill', $this->Translate('Windchill'), '~Temperature.Fahrenheit', $this->_getPosition());
            $this->RegisterVariableFloat('Dewpoint', $this->Translate('Dewpoint'), '~Temperature.Fahrenheit', $this->_getPosition());
        }

        $this->RegisterVariableFloat('Indoor_Humidity', $this->Translate('Indoor Humidity'), '~Humidity.F', $this->_getPosition());
        $this->RegisterVariableFloat('Outdoor_Humidity', $this->Translate('Outdoor Humidity'), '~Humidity.F', $this->_getPosition());
        if ($speed_unit == self::kmh) {
            $this->RegisterVariableFloat('Windspeed_km', $this->Translate('Windspeed'), '~WindSpeed.kmh', $this->_getPosition());
            $this->RegisterVariableFloat('Windspeed_ms', $this->Translate('Windspeed'), '~WindSpeed.ms', $this->_getPosition());
            $this->RegisterVariableFloat('Windgust', $this->Translate('Wind gust'), '~WindSpeed.ms', $this->_getPosition());
        } else {
            $this->RegisterVariableFloat('Windspeed_km', $this->Translate('Windspeed'), '~WindSpeed.kmh', $this->_getPosition());
            $this->RegisterVariableFloat('Windspeed_ms', $this->Translate('Windspeed'), '~WindSpeed.ms', $this->_getPosition());
            $this->RegisterVariableFloat('Windgust', $this->Translate('Windgust'), '~WindSpeed.ms', $this->_getPosition());
        }

        $this->RegisterVariableInteger('Wind_Direction', $this->Translate('Wind Direction'), '~WindDirection', $this->_getPosition());

        if ($pressure_unit == self::pascal) {
            $this->RegisterVariableFloat('absbaromin', $this->Translate('Air Pressure absolut'), '~AirPressure.F', $this->_getPosition());
            $this->RegisterVariableFloat('baromin', $this->Translate('Air Pressure'), '~AirPressure.F', $this->_getPosition());
        } else {
            $this->RegisterVariableFloat('absbaromin', $this->Translate('Air Pressure absolut'), '~AirPressure.F', $this->_getPosition());
            $this->RegisterVariableFloat('baromin', $this->Translate('Air Pressure'), '~AirPressure.F', $this->_getPosition());
        }

        $this->RegisterVariableFloat('rainin', $this->Translate('Rain'), '~Rainfall', $this->_getPosition());
        $this->RegisterVariableFloat('dailyrainin', $this->Translate('Daily Rain'), '~Rainfall', $this->_getPosition());
        $this->RegisterVariableFloat('weeklyrainin', $this->Translate('Weekly Rain'), '~Rainfall', $this->_getPosition());
        $this->RegisterVariableFloat('monthlyrainin', $this->Translate('Monthly Rain'), '~Rainfall', $this->_getPosition());
        $this->RegisterVariableFloat('solarradiation', $this->Translate('Solar Radiation'), '', $this->_getPosition());
        $this->RegisterVariableInteger('UV', $this->Translate('UV'), '', $this->_getPosition());
        $this->RegisterVariableString('Date', $this->Translate('Date'), '', $this->_getPosition());
        $this->RegisterVariableString('Software_Type', $this->Translate('Software Type'), '', $this->_getPosition());
        $this->RegisterVariableString('Action', $this->Translate('Action'), '', $this->_getPosition());
        $this->RegisterVariableInteger('Realtime', $this->Translate('Realtime'), '', $this->_getPosition());
        $this->RegisterVariableInteger('Frequence', $this->Translate('Frequence'), '', $this->_getPosition());

        $model = $this->ReadPropertyInteger('model');
        if ($model == self::ELV_WS980WiFi || $model == self::Froggit_WH4000SE) {
            $this->RegisterVariableFloat('yearrainin', $this->Translate('Year Rain'), '~Rainfall', $this->_getPosition());
            $this->RegisterVariableFloat('totalrainin', $this->Translate('Total Rain'), '~Rainfall', $this->_getPosition());
            $this->RegisterVariableFloat('heatindex', $this->Translate('heat index'), '', $this->_getPosition());
            $this->RegisterVariableFloat('illuminance', $this->Translate('illuminance'), '', $this->_getPosition());
        }

        $this->ValidateConfiguration();

    }

    /**
     * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
     * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wie folgt zur Verfügung gestellt:.
     */
    private function ValidateConfiguration()
    {
        $model = $this->ReadPropertyInteger('model');
        if ($model == self::ELV_WS980WiFi || $model == self::Froggit_WH4000SE) {
            $this->SetUpdateIntervallData();
        }
        $mac = $this->ReadPropertyString('MAC');
        if ($mac == '') {
            $this->SetStatus(201);
        } else {
            // set interval
            $this->SetUpdateIntervallWunderground();
            $this->SetUpdateIntervallWeathercloud();
            $this->SetUpdateIntervallWeatherbug();
            $this->SetUpdateIntervallAmbientWeather();
            $this->SetStatus(IS_ACTIVE);
        }
    }

    /**
     * Update Interval Wunderground
     * set / unset update interval.
     */
    protected function SetUpdateIntervallWunderground()
    {
        $interval = $this->ReadPropertyInteger('UpdateInterval_Wunderground') * 1000;
        $this->SetTimerInterval('WundergroundTimerUpdate', $interval);
    }

    /**
     * Update Interval Weathercloud
     * set / unset update interval.
     */
    protected function SetUpdateIntervallWeathercloud()
    {
        $interval = $this->ReadPropertyInteger('UpdateInterval_Weathercloud') * 1000 * 60;
        $this->SetTimerInterval('WeathercloudTimerUpdate', $interval);
    }

    /**
     * Update Interval Weatherbug
     * set / unset update interval.
     */
    protected function SetUpdateIntervallWeatherbug()
    {
        $interval = $this->ReadPropertyInteger('UpdateInterval_Weatherbug') * 1000 * 60;
        $this->SetTimerInterval('WeatherbugTimerUpdate', $interval);
    }

    /**
     * Update Interval Ambient Weather
     * set / unset update interval.
     */
    protected function SetUpdateIntervallAmbientWeather()
    {
        $interval = $this->ReadPropertyInteger('UpdateInterval_AmbientWeather') * 1000;
        $this->SetTimerInterval('AmbientWeatherTimerUpdate', $interval);
    }

    /**
     * Update Interval Ambient Weather
     * set / unset update interval.
     */
    protected function SetUpdateIntervallData()
    {
        $interval = $this->ReadPropertyInteger('UpdateInterval_Data') * 1000;
        $this->SetTimerInterval('UpdateData', $interval);
    }

    /** Find WiFi Weather Station (UDP Broadcast)
     *
     * @param string $ip
     * @param int    $port
     *
     * @return array
     */
    public function FindStation(string $ip = '255.255.255.255', int $port = 46000)
    {
        $name    = '';
        $address = '';
        $mac     = '';

        // send command {0xff, 0xff, 0x12, 0x00, 0x04, 0x16}
        $cmd = chr(0xFF) . chr(0xFF) . chr(0x12) . chr(0x00) . chr(0x04) . chr(0x16);

        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);
        // send commnd
        socket_sendto($sock, $cmd, strlen($cmd), 0, $ip, $port);
        // receive response
        socket_recvfrom($sock, $buf, 39, 0, $ip, $port);
        // HexDump($buf);
        socket_close($sock);
        // get data
        $format = 'x5/' .        // Get the first 2 bytes
                  'C6MAC/' .     // Get the next 6 byte
                  'C4IP/' .      // Get the next 4 byte
                  'n1PORT/' .    // Get the next 2 byte
                  'x1/' .        // Get the next 1 byte
                  'A20NAME/' .   // Get the next 20 byte
                  'x1';          // Get the next 1 byte
        $array  = unpack($format, $buf);
        $this->SendDebug('Weatherstation Payload', json_encode($array), 0);
        if (isset($array['NAME'])) {
            $name = $array['NAME'];
            $this->SendDebug('Weatherstation name', $name, 0);
        }
        if ($name != '') {
            $this->WriteAttributeString('weatherstation_name', $name);
        }
        if (isset($array['MAC1'])) {
            $mac = dechex($array['MAC1']) . ':' . dechex($array['MAC2']) . ':' . dechex($array['MAC3']) . ':' . dechex($array['MAC4']) . ':' . dechex(
                    $array['MAC5']
                ) . ':' . dechex($array['MAC6']);
            $this->SendDebug('Weatherstation mac', $mac, 0);
        }
        if ($mac != '') {
            $this->WriteAttributeString('weatherstation_mac', $mac);
        }
        if (isset($array['IP1'])) {
            $address = $array['IP1'] . '.' . $array['IP2'] . '.' . $array['IP3'] . '.' . $array['IP4'];
            $this->SendDebug('Weatherstation address', $address, 0);
        }
        if ($address != '') {
            $this->WriteAttributeString('weatherstation_address', $address);
        }
        if (isset($array['PORT'])) {
            $port = $array['PORT'];
            $this->SendDebug('Weatherstation port', $port, 0);
        }
        if ($port != 0) {
            $this->WriteAttributeInteger('weatherstation_port', $port);
        }
        return ['name' => $name, 'mac' => $mac, 'address' => $address, 'port' => $port];
    }

    public function GetWeatherStationAttributes()
    {
        $name    = $this->ReadAttributeString('weatherstation_name');
        $mac     = $this->ReadAttributeString('weatherstation_mac');
        $address = $this->ReadAttributeString('weatherstation_address');
        $port    = $this->ReadAttributeInteger('weatherstation_port');
        return ['name' => $name, 'mac' => $mac, 'address' => $address, 'port' => $port];
    }

    /** Get Version
     *
     * @return string
     */
    public function GetVersion()
    {
        // TCP Socket
        $ip   = $this->ReadAttributeString('weatherstation_address');
        $name = '';
        if ($ip != '') {
            $str  = chr(0xFF) . chr(0xFF) . chr(0x50) . chr(0x03) . chr(0x53);
            $buf = $this->SendData_Weatherstation($str);
            $format = '@5/' .       // Override the first 5 bytes
                      'A17Name';    // Get the next 17 byte
            $array  = unpack($format, $buf);
            $name   = $array['Name'];
        }
        return $name;
    }

    public function GetData()
    {
        // TCP Socket
        $ip   = $this->ReadAttributeString('weatherstation_address');
        $data = [];
        if ($ip != '') {
            $str  = chr(0xFF) . chr(0xFF) . chr(0x0B) . chr(0x00) . chr(0x06) . chr(0x04) . chr(0x04) . chr(0x19);
            $buf = $this->SendData_Weatherstation($str);
            $this->hex_dump($buf);
            $format = 'x7/' .                 // Override first 7 bytes
                      'n1Innentemperatur/' .  // Get the next 2 bytes
                      'x1/' .                 // Override 1 byte
                      'n1Aussentemperatur/' .  // Get the next 2 bytes
                      'x1/' .                 // Override 1 byte
                      'n1Taupunkt/' .         // Get the next 2 bytes
                      'x1/' .                 // Override 1 byte
                      'n1Gefuehlte/' .         // Get the next 2 bytes
                      'x1/' .                 // Override 1 byte
                      'n1Hitze/' .            // Get the next 2 bytes
                      'x1/' .                 // Override 1 byte
                      'C1Innenfeuchte/' .     // Get the next 1 byte
                      'x1/' .                 // Override 1 byte
                      'C1Aussenfeuchte/' .    // Get the next 1 byte
                      'x1/' .                 // Override 1 byte
                      'n1AbsDruck/' .         // Get the next 2 bytes
                      'x1/' .                 // Override 1 byte
                      'n1RelDruck/' .         // Get the next 2 bytes
                      'x1/' .                 // Override 1 byte
                      'n1Windrichtung/' .     // Get the next 2 bytes
                      'x1/' .                 // Override 1 byte
                      'n1Windspeed/' .        // Get the next 2 bytes
                      'x1/' .                 // Override 1 byte
                      'n1Windboe/' .          // Get the next 2 bytes
                      'x1/' .                 // Override 1 byte
                      'N1RegenH/' .           // Get the next 4 bytes
                      'x1/' .                 // Override 1 byte
                      'N1RegenD/' .           // Get the next 4 bytes
                      'x1/' .                 // Override 1 byte
                      'N1RegenW/' .           // Get the next 4 bytes
                      'x1/' .                 // Override 1 byte
                      'N1RegenM/' .           // Get the next 4 bytes
                      'x1/' .                 // Override 1 byte
                      'N1RegenY/' .           // Get the next 4 bytes
                      'x1/' .                 // Override 1 byte
                      'N1RegenS/' .           // Get the next 4 bytes
                      'x1/' .                 // Override 1 byte
                      'N1Licht/' .            // Get the next 4 bytes
                      'x1/' .                 // Override 1 byte
                      'n1UvRaw/' .            // Get the next 2 bytes
                      'x1/' .                 // Override 1 byte
                      'C1UvIdx';           // Get the next 2 bytes

            $data = unpack($format, $buf);
            if ($data['Taupunkt'] >= pow(2, 15)) {
                $data['Taupunkt'] -= pow(2, 16);
            }
            if ($data['Innentemperatur'] >= pow(2, 15)) {
                $data['Innentemperatur'] -= pow(2, 16);
            }
            if ($data['Aussentemperatur'] >= pow(2, 15)) {
                $data['Aussentemperatur'] -= pow(2, 16);
            }
            if ($data['Gefuehlte'] >= pow(2, 15)) {
                $data['Gefuehlte'] -= pow(2, 16);
            }
            if ($data['Hitze'] >= pow(2, 15)) {
                $data['Hitze'] -= pow(2, 16);
            }
            $temp_unit     = $this->ReadPropertyInteger('temp_unit');
            $speed_unit    = $this->ReadPropertyInteger('speed_unit');
            $pressure_unit = $this->ReadPropertyInteger('pressure_unit');

            if (isset($data['Innentemperatur'])) {
                $indoor_temperature = floatval($data['Innentemperatur'] / 10.);
                $this->SendDebug('Weatherstation:', 'indoor temperature: ' . $indoor_temperature, 0);
                if ($temp_unit == 1) {
                    $this->SetValue('Indoor_Temp', $this->FahrenheitToCelsius($indoor_temperature));
                } else {
                    $this->SetValue('Indoor_Temp', floatval($indoor_temperature));
                }
            }
            if (isset($data['Aussentemperatur'])) {
                $temperature = floatval($data['Aussentemperatur'] / 10.);
                $this->SendDebug('Weatherstation:', 'temperature: ' . $temperature, 0);
                if ($temp_unit == 1) {
                    $this->SetValue('Outdoor_Temp', $this->FahrenheitToCelsius($temperature));
                } else {
                    $this->SetValue('Outdoor_Temp', floatval($temperature));
                }
            }
            if (isset($data['Taupunkt'])) {
                $dewpoint = floatval($data['Taupunkt'] / 10.);
                $this->SendDebug('Weatherstation:', 'dewpoint: ' . $dewpoint, 0);
                if ($temp_unit == 1) {
                    $this->SetValue('Dewpoint', $this->FahrenheitToCelsius($dewpoint));
                } else {
                    $this->SetValue('Dewpoint', floatval($dewpoint));
                }
            }
            if (isset($data['Gefuehlte'])) {
                $windchill = floatval($data['Gefuehlte'] / 10.);
                $this->SendDebug('Weatherstation:', 'windchill: ' . $windchill, 0);
                if ($temp_unit == 1) {
                    $this->SetValue('Windchill', $this->FahrenheitToCelsius($windchill));
                } else {
                    $this->SetValue('Windchill', floatval($windchill));
                }
            }
            if (isset($data['Innenfeuchte'])) {
                $indoorhumidity = $data['Innenfeuchte'] / 100.;
                $this->SendDebug('Weatherstation:', 'indoor humidity: ' . $indoorhumidity, 0);
                $this->SetValue('Indoor_Humidity', floatval($indoorhumidity));
            }
            if (isset($data['Aussenfeuchte'])) {
                $humidity = $data['Aussenfeuchte'] / 100.;
                $this->SendDebug('Weatherstation:', 'humidity: ' . $humidity, 0);
                $this->SetValue('Outdoor_Humidity', floatval($humidity));
            }
            if (isset($data['Windspeed'])) {
                $windspeed = floatval(($data['Windspeed'] / 10.) * 3.6);
                $this->WriteWindSpeed($windspeed);
            }
            if (isset($data['Windboe'])) {
                $windgust = floatval(($data['Windboe'] / 10.) * 3.6);
                $this->SendDebug('Weatherstation:', 'windgust: ' . $windgust, 0);
                if ($speed_unit == self::kmh) {
                    $this->SetValue('Windgust', $this->MilesToKilometer($windgust));
                } else {
                    $this->SetValue('Windgust', $windgust);
                }
            }
            if (isset($data['Windrichtung'])) {
                $winddir = $data['Windrichtung'];
                $this->SendDebug('Weatherstation:', 'wind direction: ' . $winddir, 0);
                $this->SetValue('Wind_Direction', intval($winddir));
            }
            if (isset($data['AbsDruck'])) {
                $absbaromin = floatval($data['AbsDruck'] / 10.);
                $this->SendDebug('Weatherstation:', 'abs barometer min: ' . $absbaromin, 0);
                if ($pressure_unit == 1) {
                    $this->SetValue('absbaromin', $this->Pressure_absolute($absbaromin));
                } else {
                    $this->SetValue('absbaromin', $absbaromin);
                }
            }
            if (isset($data['RelDruck'])) {
                $baromin = floatval($data['RelDruck'] / 10.);
                $this->SendDebug('Weatherstation:', 'barometer min: ' . $baromin, 0);
                if ($pressure_unit == 1) {
                    $this->SetValue('baromin', $this->Pressure($baromin, $this->FahrenheitToCelsius($temperature)));
                } else {
                    $this->SetValue('baromin', $baromin);
                }
            }
            if (isset($data['RegenH'])) {
                $rainin = floatval($data['RegenH'] / 10.);
                $this->SendDebug('Weatherstation:', 'rain: ' . $rainin, 0);
                $this->SetValue('rainin', $this->Rain($rainin));
            }
            if (isset($data['RegenD'])) {
                $dailyrainin = $data['RegenD'] / 10.;
                $this->SendDebug('Weatherstation:', 'daily rain: ' . $dailyrainin, 0);
                $this->SetValue('dailyrainin', $dailyrainin);
            }
            if (isset($data['RegenW'])) {
                $weeklyrainin = $data['RegenW'] / 10.;
                $this->SendDebug('Weatherstation:', 'weekly rain: ' . $weeklyrainin, 0);
                $this->SetValue('weeklyrainin', $weeklyrainin);
            }
            if (isset($data['RegenM'])) {
                $monthlyrainin = $data['RegenM'] / 10.;
                $this->SendDebug('Weatherstation:', 'monthly rain: ' . $monthlyrainin, 0);
                $this->SetValue('monthlyrainin', $monthlyrainin);
            }
            if (isset($data['RegenY'])) {
                $yearrainin = $data['RegenY'] / 10.;
                $this->SendDebug('Weatherstation:', 'rain year: ' . $yearrainin, 0);
                $this->SetValue('yearrainin', $yearrainin);
            }
            if (isset($data['RegenS'])) {
                $totalrainin = $data['RegenS'] / 10.;
                $this->SendDebug('Weatherstation:', 'total rain: ' . $totalrainin, 0);
                $this->SetValue('totalrainin', $totalrainin);
            }
            if (isset($data['UvRaw'])) {
                $solarradiation = $data['UvRaw'];
                $this->SendDebug('Weatherstation:', 'solar radiation: ' . $solarradiation, 0);
                $this->SetValue('solarradiation', floatval($solarradiation));
            }
            if (isset($data['UvIdx'])) {
                $uv = $data['UvIdx'];
                $this->SendDebug('Weatherstation:', 'uv: ' . $uv, 0);
                $this->SetValue('UV', intval($uv));
            }
            if (isset($data['Hitze'])) {
                $heatindex = $data['Hitze'] / 10.;
                $this->SendDebug('Weatherstation:', 'heat index: ' . $heatindex, 0);
                $this->SetValue('heatindex', $heatindex);
            }
            if (isset($data['Licht'])) {
                $illuminance = $data['Licht'] / 10.;
                $this->SendDebug('Weatherstation:', 'rt freq: ' . $illuminance, 0);
                $this->SetValue('illuminance', $illuminance);
            }
            $this->WriteCommonData($data);
        }
        return $data;
    }

    private function SendData_Weatherstation($str)
    {
        $ip    = $this->ReadAttributeString('weatherstation_address');
        $port = $this->ReadAttributeInteger('weatherstation_port');
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($sock === false) {
            $this->SendDebug('Weatherstation Socket', 'socket_create() failed: ' . socket_strerror(socket_last_error()), 0);
        }
        if (socket_connect($sock, $ip, $port) === false) {
            $this->SendDebug('Weatherstation Socket', 'socket_connect() failed: ' . socket_strerror(socket_last_error()), 0);
        }
        $len = socket_write($sock, $str, strlen($str));
        if (false === $len) {
            $this->SendDebug('Weatherstation Socket', 'socket_write() failed: ' . socket_strerror(socket_last_error()), 0);
        }
        if (false === ($buf = socket_read($sock, 2048, PHP_BINARY_READ))) {
            $this->SendDebug('Weatherstation Socket', 'socket_read() failed: ' . socket_strerror(socket_last_error()), 0);
        }
        socket_close($sock);
        return $buf;
    }

    private function hex_dump($data, $newline = "\n")
    {
        static $from = '';
        static $to = '';
        static $width = 16; // number of bytes per line
        static $pad = '.'; // padding for non-visible characters

        if ($from === '') {
            for ($i = 0; $i <= 0xFF; $i++) {
                $from .= chr($i);
                $to   .= ($i >= 0x20 && $i <= 0x7E) ? chr($i) : $pad;
            }
        }
        $hex    = str_split(bin2hex($data), $width * 2);
        $chars  = str_split(strtr($data, $from, $to), $width);
        $offset = 0;
        foreach ($hex as $i => $line) {
            $this->SendDebug(
                'Weatherstation HexSocket',
                sprintf('%06X', $offset) . ' : ' . strtoupper(implode(' ', str_split($line, 2))) . ' [' . $chars[$i] . ']' . $newline, 0
            );
            $offset += $width;
        }
    }

    protected function CelsiusToFahrenheit(float $celsius)
    {
        return $celsius * 1.8 + 32;
    }

    protected function FahrenheitToCelsius(float $fahrenheit)
    {
        return ($fahrenheit - 32) / 1.8;
    }

    protected function MilesToKilometer(float $mph)
    {
        $kmh = $mph * 1.609344;
        return $kmh;
    }

    protected function MilesToKN(float $mph)
    {
        $kn = $mph * 0.86897624190065;
        return $kn;
    }

    protected function KilometerToKN(float $kmh)
    {
        $kn = $kmh / 1.852;
        return $kn;
    }

    protected function MPHToMS(float $mph)
    {
        $ms = $mph * 0.44704;
        return $ms;
    }

    protected function MSToMPH(float $ms)
    {
        $mph = $ms * 2.23694;
        return $mph;
    }

    protected function Rain(float $inch)
    {
        $mm = $inch * 25.4;
        return $mm;
    }

    protected function RainToInch(float $mm)
    {
        $inch = $mm * 0.03937007874;
        return $inch;
    }

    protected function Pressure_absolute(float $pressure)
    {
        $pascal = $pressure / 0.02952998751;
        return $pascal;
    }

    protected function Pressure(float $pressure, float $temperature)
    {
        $pascal   = $pressure / 0.02952998751;
        $altitude = $this->ReadPropertyInteger('altitude_above_sea_level');

        $g0 = 9.80665;                                         // Normwert der Fallbeschleunigung
        $R  = 287.05;                                          // Gaskonstante trockener Luft
        $T  = 273.15;                                          // 0°C in Kelvin
        $Ch = 0.12;                                            // Beiwert zu E
        if ($temperature < 9.1) {
            $E = 5.6402 * (-0.0916 + exp(0.06 * $temperature));        // Dampfdruck des Wasserdampfanteils bei t < 9.1°C
        } else {
            $E = 18.2194 * (1.0463 - exp(-0.0666 * $temperature));    // Dampfdruck des Wasserdampfanteils bei t >= 9.1°C
        }
        $a  = 0.0065;                                          // vertikaler Temperaturgradient
        $xp = $altitude * $g0 / ($R * ($T + $temperature + $Ch * $E + $a * $altitude / 2)); // Exponent für Formel
        $p0 = $pascal * exp($xp);                             // Formel für den NN-bezogenen Luftdruck laut Wikipedia
        return $p0;
    }

    protected function PressurehPaToBar($pressure)
    {
        $bar = $pressure * 0.02952998751;
        return $bar;
    }

    protected function SendToIO(string $payload)
    {
        $result =
            $this->SendDataToParent(json_encode(['DataID' => '{C8792760-65CF-4C53-B5C7-A30FCC84FEFE}', 'Buffer' => $payload])); // TX Server Socket
        return $result;
    }

    public function ReceiveData($JSONString)
    {

        $this->SendDebug('Weatherstation:', $JSONString, 0);
        $payload = json_decode($JSONString);
        if (isset($payload->Type)) {
            $type = $payload->Type;
            if ($type == 0) {
                $this->SendDebug('Weatherstation:', json_encode($payload->Buffer), 0);
                $this->WriteData($payload->Buffer);
            }
        } else {
            $this->SendDebug('Weatherstation:', json_encode($payload->Buffer), 0);
            $this->WriteData($payload->Buffer);
        }
    }

    protected function WriteData($payloadraw)
    {
        $payload = substr($payloadraw, 4, strlen($payloadraw) - 4);
        $address = $this->ReadAttributeString('weatherstation_address');
        if ($address == '') {
            $address = '192.168.1.1';
        }
        $this->SendDebug('Weatherstation payload:', $payload, 0);
        $first_char = substr($payload, 0, 1);
        if ($first_char == '/') {
            $url = 'http://' . $address . $payload;
        } else {
            $pos_id = strpos($payload, 'ID');
            if($pos_id == 0)
            {
                $payload = '?' . $payload;
            }
            $pos_http = strpos($payload, 'HTTP/1.0');
            $payload = substr($payload, 0, $pos_http-1);
            $url = 'http://' . $address . '/' . $payload;
        }
        $this->SendDebug('Weatherstation:', $url, 0);
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $data);
        $temp_unit     = $this->ReadPropertyInteger('temp_unit');
        $speed_unit    = $this->ReadPropertyInteger('speed_unit');
        $pressure_unit = $this->ReadPropertyInteger('pressure_unit');
        if (isset($data['indoortempf'])) {
            $indoor_temperature = floatval($data['indoortempf']);
            $this->SendDebug('Weatherstation:', 'indoor temperature: ' . $indoor_temperature, 0);
            if ($temp_unit == 1) {
                $this->SetValue('Indoor_Temp', $this->FahrenheitToCelsius($indoor_temperature));
            } else {
                $this->SetValue('Indoor_Temp', $indoor_temperature);
            }
        }
        if (isset($data['tempf'])) {
            $temperature = floatval($data['tempf']);
            $this->SendDebug('Weatherstation:', 'temperature: ' . $temperature, 0);
            if ($temp_unit == 1) {
                $this->SetValue('Outdoor_Temp', $this->FahrenheitToCelsius($temperature));
            } else {
                $this->SetValue('Outdoor_Temp', $temperature);
            }
        }
        if (isset($data['dewptf'])) {
            $dewpoint = floatval($data['dewptf']);
            $this->SendDebug('Weatherstation:', 'dewpoint: ' . $dewpoint, 0);
            if ($temp_unit == 1) {
                $this->SetValue('Dewpoint', $this->FahrenheitToCelsius($dewpoint));
            } else {
                $this->SetValue('Dewpoint', $dewpoint);
            }
        }
        if (isset($data['windchillf'])) {
            $windchill = floatval($data['windchillf']);
            $this->SendDebug('Weatherstation:', 'windchill: ' . $windchill, 0);
            if ($temp_unit == 1) {
                $this->SetValue('Windchill', $this->FahrenheitToCelsius($windchill));
            } else {
                $this->SetValue('Windchill', $windchill);
            }
        }
        if (isset($data['indoorhumidity'])) {
            $indoorhumidity = floatval($data['indoorhumidity']);
            $this->SendDebug('Weatherstation:', 'indoor humidity: ' . $indoorhumidity, 0);
            $this->SetValue('Indoor_Humidity', $indoorhumidity);
        }
        if (isset($data['humidity'])) {
            $humidity = floatval($data['humidity']);
            $this->SendDebug('Weatherstation:', 'humidity: ' . $humidity, 0);
            $this->SetValue('Outdoor_Humidity', $humidity);
        }
        if (isset($data['windspeedmph'])) {
            $windspeed = floatval($data['windspeedmph']);
            $this->WriteWindSpeed($windspeed);
        }
        if (isset($data['windgustmph'])) {
            $windgust = floatval($data['windgustmph']);
            $this->SendDebug('Weatherstation:', 'windgust: ' . $windgust, 0);
            if ($speed_unit == self::kmh) {
                $this->SetValue('Windgust', $this->MPHToMS($windgust));
            } else {
                $this->SetValue('Windgust', $windgust);
            }
        }
        if (isset($data['winddir'])) {
            $winddir = $data['winddir'];
            $this->SendDebug('Weatherstation:', 'wind direction: ' . $winddir, 0);
            $this->SetValue('Wind_Direction', intval($winddir));
        }
        if (isset($data['wi_nddir'])) {
            $winddir = $data['winddir'];
            $this->SendDebug('Weatherstation:', 'wind direction: ' . $winddir, 0);
            $this->SetValue('Wind_Direction', intval($winddir));
        }
        if (isset($data['absbaromin'])) {
            $absbaromin = floatval($data['absbaromin']);
            $this->SendDebug('Weatherstation:', 'abs barometer min: ' . $absbaromin, 0);
            if ($pressure_unit == self::pascal) {
                $this->SetValue('absbaromin', $this->Pressure_absolute($absbaromin));
            } else {
                $this->SetValue('absbaromin', $absbaromin);
            }
        }
        if (isset($data['baromin'])) {
            $baromin = floatval($data['baromin']);
            $this->SendDebug('Weatherstation:', 'barometer min: ' . $baromin, 0);
            if ($pressure_unit == self::pascal) {
                $this->SetValue('baromin', $this->Pressure($baromin, $this->FahrenheitToCelsius($temperature)));
            } else {
                $this->SetValue('baromin', $baromin);
            }
        }
        if (isset($data['rainin'])) {
            $rainin = floatval($data['rainin']);
            $this->SendDebug('Weatherstation:', 'rain: ' . $rainin, 0);
            $this->SetValue('rainin', $this->Rain($rainin));
        }
        if (isset($data['dailyrainin'])) {
            $dailyrainin = floatval($data['dailyrainin']);
            $this->SendDebug('Weatherstation:', 'daily rain: ' . $dailyrainin, 0);
            $this->SetValue('dailyrainin', $this->Rain($dailyrainin));
        }
        if (isset($data['weeklyrainin'])) {
            $weeklyrainin = floatval($data['weeklyrainin']);
            $this->SendDebug('Weatherstation:', 'weekly rain: ' . $weeklyrainin, 0);
            $this->SetValue('weeklyrainin', $this->Rain($weeklyrainin));
        }
        if (isset($data['monthlyrainin'])) {
            $monthlyrainin = floatval($data['monthlyrainin']);
            $this->SendDebug('Weatherstation:', 'monthly rain: ' . $monthlyrainin, 0);
            $this->SetValue('monthlyrainin', $this->Rain($monthlyrainin));
        }
        if (isset($data['solarradiation'])) {
            $solarradiation = floatval($data['solarradiation']);
            $this->SendDebug('Weatherstation:', 'solar radiation: ' . $solarradiation, 0);
            $this->SetValue('solarradiation', $solarradiation);
        }
        if (isset($data['UV'])) {
            $uv = $data['UV'];
            $this->SendDebug('Weatherstation:', 'uv: ' . $uv, 0);
            $this->SetValue('UV', intval($uv));
        }
        $this->WriteCommonData($data);
    }

    private function WriteCommonData($data)
    {
        if (isset($data['dateutc'])) {
            $dateutc = $data['dateutc'];
            $this->SendDebug('Weatherstation:', 'date utc: ' . $dateutc, 0);
            $this->SetValue('Date', $dateutc);
        }
        if (isset($data['softwaretype'])) {
            $softwaretype = $data['softwaretype'];
            $this->SendDebug('Weatherstation:', 'software type: ' . $softwaretype, 0);
            $this->SetValue('Software_Type', $softwaretype);
        }
        if (isset($data['action'])) {
            $action = $data['action'];
            $this->SendDebug('Weatherstation:', 'action: ' . $action, 0);
            $this->SetValue('Action', $action);
        }
        if (isset($data['realtime'])) {
            $realtime = $data['realtime'];
            $this->SendDebug('Weatherstation:', 'realtime: ' . $realtime, 0);
            $this->SetValue('Realtime', intval($realtime));
        }
        if (isset($data['rtfreq'])) {
            $rtfreq = $data['rtfreq'];
            $this->SendDebug('Weatherstation:', 'rt freq: ' . $rtfreq, 0);
            $this->SetValue('Frequence', intval($rtfreq));
        }
    }

    private function WriteWindSpeed($windspeed)
    {
        $speed_unit    = $this->ReadPropertyInteger('speed_unit');
        $this->SendDebug('Weatherstation:', 'windspeed: ' . $windspeed, 0);
        if ($speed_unit == self::kmh) {
            $this->SetValue('Windspeed_km', $this->MilesToKilometer($windspeed));
            $this->SetValue('Windspeed_ms', $this->MPHToMS($windspeed));
        } else {
            $this->SetValue('Windspeed_km', $windspeed);
            $this->SetValue('Windspeed_ms', $this->MPHToMS($windspeed));
        }
    }

    public function Update_Wunderground()
    {
        $wunderground_url              = 'https://weatherstation.wunderground.com/weatherstation/updateweatherstation.php';
        $wunderground_station_id       = $this->ReadPropertyString('Wunderground_Station_ID');
        $wunderground_station_password = $this->ReadPropertyString('Wunderground_Station_Password');
        // get data for wunderground

        $param = $this->GetParametersWunderground();
        $url = $wunderground_url . '?ID=' . $wunderground_station_id . '&PASSWORD=' . $wunderground_station_password . '&action=updateraw' . $param;
        $this->SendData($url, 'wunderground');
    }

    protected function GetParametersWunderground()
    {
        $param = '&dateutc=now';
        // $param = '&dateutc=' . rawurlencode(date('Y-m-d G:i:s', time()));
        $param .= '&indoortempf=' . str_replace(',', '.', strval($this->CelsiusToFahrenheit(GetValueFloat($this->GetIDForIdent('Indoor_Temp')))));
        $param .= '&tempf=' . str_replace(',', '.', strval($this->CelsiusToFahrenheit(GetValueFloat($this->GetIDForIdent('Outdoor_Temp')))));
        $param .= '&dewptf=' . str_replace(',', '.', strval($this->CelsiusToFahrenheit(GetValueFloat($this->GetIDForIdent('Dewpoint')))));
        $param .= '&windchillf=' . str_replace(',', '.', strval($this->CelsiusToFahrenheit(GetValueFloat($this->GetIDForIdent('Windchill')))));
        $param .= '&indoorhumidity=' . str_replace(',', '.', GetValue($this->GetIDForIdent('Indoor_Humidity')));
        $param .= '&humidity=' . str_replace(',', '.', GetValue($this->GetIDForIdent('Outdoor_Humidity')));
        $param .= '&windspeedmph=' . str_replace(',', '.', strval($this->MSToMPH(GetValueFloat($this->GetIDForIdent('Windspeed_ms')))));
        $param .= '&windgustmph=' . str_replace(',', '.', strval($this->MSToMPH(GetValueFloat($this->GetIDForIdent('Windgust')))));
        $param .= '&winddir=' . str_replace(',', '.', strval(GetValue($this->GetIDForIdent('Wind_Direction'))));
        $param .= '&absbaromin=' . str_replace(',', '.', $this->PressurehPaToBar(GetValue($this->GetIDForIdent('absbaromin'))));
        $param .= '&baromin=' . str_replace(',', '.', $this->PressurehPaToBar(GetValue($this->GetIDForIdent('baromin'))));
        $param .= '&rainin=' . str_replace(',', '.', strval($this->RainToInch(GetValueFloat($this->GetIDForIdent('rainin')))));
        $param .= '&dailyrainin=' . str_replace(',', '.', strval($this->RainToInch(GetValueFloat($this->GetIDForIdent('dailyrainin')))));
        $param .= '&weeklyrainin=' . str_replace(',', '.', strval($this->RainToInch(GetValueFloat($this->GetIDForIdent('weeklyrainin')))));
        $param .= '&monthlyrainin=' . str_replace(',', '.', strval($this->RainToInch(GetValueFloat($this->GetIDForIdent('monthlyrainin')))));
        $param .= '&solarradiation=' . str_replace(',', '.', GetValue($this->GetIDForIdent('solarradiation')));
        $param .= '&UV=' . str_replace(',', '.', GetValue($this->GetIDForIdent('UV')));
        $param .= '&softwaretype=' . GetValue($this->GetIDForIdent('Software_Type'));
        $param .= '&realtime=1';
        $param .= '&rtfreq=20';
        return $param;
    }

    public function Update_Weathercloud()
    {
        $weathercloud_url = 'http://api.weathercloud.net/v01/set';

        $weathercloud_station_id       = $this->ReadPropertyString('Weathercloud_ID');
        $weathercloud_station_password = $this->ReadPropertyString('Weathercloud_Key');

        $url = $weathercloud_url . '?wid=' . $weathercloud_station_id . '&key=' . $weathercloud_station_password;

        $param = $this->GetParametersWeathercloud();
        $url   = $url . $param;
        $this->SendData($url, 'weathercloud');
    }

    protected function GetParametersWeathercloud()
    {
        // Gesendet wird noch von Station  "rainrate" und "heat" ??

        $param = '&date=' . date('Ymd', time() - date('Z'));
        $param .= '&time=' . date('Hi', time() - date('Z'));
        $param .= '&tempin=' . intval(GetValue($this->GetIDForIdent('Indoor_Temp')) * 10);
        $param .= '&temp=' . intval(GetValue($this->GetIDForIdent('Outdoor_Temp')) * 10);
        $param .= '&dew=' . intval(GetValue($this->GetIDForIdent('Dewpoint')) * 10);
        $param .= '&chill=' . intval(GetValue($this->GetIDForIdent('Windchill')) * 10);
        $param .= '&humin=' . intval(GetValue($this->GetIDForIdent('Indoor_Humidity')));
        $param .= '&hum=' . intval(GetValue($this->GetIDForIdent('Outdoor_Humidity')));
        $param .= '&wspd=' . intval($this->KilometerToKN(GetValueFloat($this->GetIDForIdent('Windspeed_ms'))) * 10);
        $param .= '&wspdhi=' . intval($this->KilometerToKN(GetValueFloat($this->GetIDForIdent('Windgust'))) * 10);
        $param .= '&wspdavg=' . intval($this->KilometerToKN(GetValueFloat($this->GetIDForIdent('Windgust'))) * 10);
        $param .= '&wdir=' . intval(GetValue($this->GetIDForIdent('Wind_Direction')));
        $param .= '&bar=' . intval(GetValue($this->GetIDForIdent('baromin')) * 10);
        $param .= '&rain=' . intval(GetValue($this->GetIDForIdent('rainin')));
        $param .= '&solarrad=' . intval(GetValue($this->GetIDForIdent('solarradiation')) * 10);
        $param .= '&uvi=' . intval(GetValue($this->GetIDForIdent('UV')));
        $param .= '&type=EasyWeather';
        $param .= '&ver=1.2.1';

        return $param;
    }

    private function SendData($url, $weatherservice)
    {
        $this->SendDebug('Weatherstation:', 'http-get: url=' . $url, 0);
        $time_start = microtime(true);
        $duration   = floor((microtime(true) - $time_start) * 100) / 100;
        $ch         = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $wstatus  = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $this->SendDebug('Weatherstation:', ' => httpcode=' . $httpcode . ', duration=' . $duration . 's', 0);
        if ($httpcode != 200) {
            $err = " => got http-code $httpcode from ambient weather";
            $this->SendDebug('Weatherstation:', $err, 0);
        }
        if ($wstatus) {
            $wstatus = trim($wstatus, "\n");
        } else {
            $wstatus = 'false';
        }
        if ($wstatus != 'success') {
            $err = ' => got from ' . $weatherservice . ': ' . $wstatus;
            $this->SendDebug('Weatherstation:', $err, 0);
        }
    }

    public function Update_AmbientWeatherCloud()
    {
        $ambient_weather_cloud_url = 'https://api.ambientweather.net:80/endpoint?';
        $passkey                   = $this->ReadPropertyString('Ambient_Passkey');

        $param = $this->GetParametersAmbientWeatherCloud();
        $url   = $ambient_weather_cloud_url . '?PASSKEY=' . $passkey . '&stationtype=WS-1600-IP' . $param;
        $this->SendData($url, 'ambient weather');
    }

    protected function GetParametersAmbientWeatherCloud()
    {
        $param = '&stationtype=WS-1600-IP';
        $param .= '&dateutc=' . rawurlencode(date('Y-m-d+G:i:s', time()));
        $param .= '&winddir=' . rawurlencode(strval(GetValue($this->GetIDForIdent('Wind_Direction'))));
        $param .= '&windspeedmph=' . rawurlencode(strval($this->FormatFloat($this->MSToMPH(GetValueFloat($this->GetIDForIdent('Windspeed_ms'))))));
        $param .= '&windgustmph=' . rawurlencode(strval($this->FormatFloat($this->MSToMPH(GetValueFloat($this->GetIDForIdent('Windgust'))))));
        // &maxdailygust=4.47
        $param .= '&tempf=' . rawurlencode(strval($this->CelsiusToFahrenheit(GetValueFloat($this->GetIDForIdent('Outdoor_Temp')))));
        $param .= '&humidity=' . rawurlencode(strval(GetValue($this->GetIDForIdent('Outdoor_Humidity'))));
        // &hourlyrainin=0.00
        $param .= '&dailyrainin=' . rawurlencode(strval($this->FormatFloat(GetValue($this->GetIDForIdent('dailyrainin')))));
        $param .= '&weeklyrainin=' . rawurlencode(strval($this->FormatFloat(GetValue($this->GetIDForIdent('weeklyrainin')))));
        $param .= '&monthlyrainin=' . rawurlencode(strval($this->FormatFloat(GetValue($this->GetIDForIdent('monthlyrainin')))));
        // &yearlyrainin=0.00
        // &totalrainin=0.00
        $param .= '&tempinf=' . rawurlencode(strval($this->CelsiusToFahrenheit(GetValueFloat($this->GetIDForIdent('Indoor_Temp')))));
        $param .= '&humidityin=' . rawurlencode(strval(intval(GetValue($this->GetIDForIdent('Indoor_Humidity')))));
        $param .= '&baromrelin=' . rawurlencode(strval($this->FormatFloat(GetValue($this->GetIDForIdent('baromin')))));
        $param .= '&baromabsin=' . rawurlencode(strval($this->FormatFloat(GetValue($this->GetIDForIdent('absbaromin')))));
        $param .= '&uv=' . rawurlencode(strval(GetValue($this->GetIDForIdent('UV'))));
        $param .= '&solarradiation=' . rawurlencode(strval(GetValue($this->GetIDForIdent('solarradiation'))));
        /*
        $param .= '&dewptf=' . rawurlencode(strval($this->CelsiusToFahrenheit(GetValue($this->GetIDForIdent("Dewpoint")))));
        $param .= '&windchillf=' . rawurlencode(strval($this->CelsiusToFahrenheit(GetValue($this->GetIDForIdent("Windchill")))));
        $param .= '&rainin=' . rawurlencode(strval($this->CelsiusToFahrenheit(GetValue($this->GetIDForIdent("rainin")))));
        */
        return $param;
    }

    public function Update_Weatherbug()
    {

    }

    public function AlexaData()
    {
        $data                     = [];
        $data['Indoor_Temp']      = GetValue($this->GetIDForIdent('Indoor_Temp'));
        $data['Outdoor_Temp']     = GetValue($this->GetIDForIdent('Outdoor_Temp'));
        $data['Dewpoint']         = GetValue($this->GetIDForIdent('Dewpoint'));
        $data['Windchill']        = GetValue($this->GetIDForIdent('Windchill'));
        $data['Indoor_Humidity']  = GetValue($this->GetIDForIdent('Indoor_Humidity'));
        $data['Outdoor_Humidity'] = GetValue($this->GetIDForIdent('Outdoor_Humidity'));
        $data['Windspeed_km']     = GetValue($this->GetIDForIdent('Windspeed_km'));
        $data['Windgust']         = GetValue($this->GetIDForIdent('Windgust'));
        $data['Wind_Direction']   = GetValue($this->GetIDForIdent('Wind_Direction'));
        $data['absbaromin']       = GetValue($this->GetIDForIdent('absbaromin'));
        $data['baromin']          = GetValue($this->GetIDForIdent('baromin'));
        $data['rainin']           = GetValue($this->GetIDForIdent('rainin'));
        $data['dailyrainin']      = GetValue($this->GetIDForIdent('dailyrainin'));
        $data['weeklyrainin']     = GetValue($this->GetIDForIdent('weeklyrainin'));
        $data['monthlyrainin']    = GetValue($this->GetIDForIdent('monthlyrainin'));
        $data['solarradiation']   = GetValue($this->GetIDForIdent('solarradiation'));
        $data['UV']               = GetValue($this->GetIDForIdent('UV'));

        return $data;
    }

    /**
     * gets current IP-Symcon version.
     *
     * @return float|int
     */
    protected function GetIPSVersion()
    {
        $ipsversion = floatval(IPS_GetKernelVersion());
        if ($ipsversion < 4.1) // 4.0
        {
            $ipsversion = 0;
        } elseif ($ipsversion >= 4.1 && $ipsversion < 4.2) // 4.1
        {
            $ipsversion = 1;
        } elseif ($ipsversion >= 4.2 && $ipsversion < 4.3) // 4.2
        {
            $ipsversion = 2;
        } elseif ($ipsversion >= 4.3 && $ipsversion < 4.4) // 4.3
        {
            $ipsversion = 3;
        } elseif ($ipsversion >= 4.4 && $ipsversion < 5) // 4.4
        {
            $ipsversion = 4;
        } else   // 5
        {
            $ipsversion = 5;
        }

        return $ipsversion;
    }

    //Profile
    protected function RegisterProfile($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits, $Vartype)
    {

        if (!IPS_VariableProfileExists($Name)) {
            IPS_CreateVariableProfile($Name, $Vartype); // 0 boolean, 1 int, 2 float, 3 string,
        } else {
            $profile = IPS_GetVariableProfile($Name);
            if ($profile['ProfileType'] != $Vartype) {
                $this->SendDebug('Weatherstation:', 'Variable profile type does not match for profile ' . $Name, 0);
            }
        }

        IPS_SetVariableProfileIcon($Name, $Icon);
        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
        if ($Vartype != VARIABLETYPE_STRING) {
            IPS_SetVariableProfileDigits($Name, $Digits); //  Nachkommastellen
            IPS_SetVariableProfileValues(
                $Name, $MinValue, $MaxValue, $StepSize
            ); // string $ProfilName, float $Minimalwert, float $Maximalwert, float $Schrittweite
        }
    }

    protected function RegisterProfileAssociation($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Vartype, $Associations)
    {
        if (count($Associations) === 0) {
            $MinValue = 0;
            $MaxValue = 0;
        }

        $this->RegisterProfile($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Vartype);

        //boolean IPS_SetVariableProfileAssociation ( string $ProfilName, float $Wert, string $Name, string $Icon, integer $Farbe )
        foreach ($Associations as $Association) {
            IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
        }

    }

    protected function FormatFloat($value)
    {
        $formatted_value = str_replace(',', '.', $value);
        return $formatted_value;
    }

    /**
     * return incremented position.
     *
     * @return int
     */
    private function _getPosition()
    {
        $this->position++;
        return $this->position;
    }

    /***********************************************************
     * Configuration Form
     ***********************************************************/

    /**
     * build configuration form.
     *
     * @return string
     */
    public function GetConfigurationForm()
    {
        // return current form
        return json_encode(
            [
                'elements' => $this->FormHead(),
                'actions'  => $this->FormActions(),
                'status'   => $this->FormStatus()]
        );
    }

    /**
     * return form configurations on configuration step.
     *
     * @return array
     */
    protected function FormHead()
    {
        // $altidude = $this->altitude_above_sea_level();
        $form    = [
            [
                'name'    => 'model',
                'type'    => 'Select',
                'caption' => 'Model',
                'options' => [
                    [
                        'label' => 'Please select model',
                        'value' => 0],
                    [
                        'label' => 'Sainlogic (Firmware 1.3.8 or above)',
                        'value' => self::Sainlogic],
                    [
                        'label' => 'ELV WS980WiFi',
                        'value' => self::ELV_WS980WiFi],
                    [
                        'label' => 'Froggit WH4000SE',
                        'value' => self::Froggit_WH4000SE]]]];
        $address = $this->ReadAttributeString('weatherstation_address');
        if ($address != '') {
            $form = array_merge_recursive(
                $form, [
                    [
                        'type'     => 'List',
                        'name'     => 'weatherstation_info',
                        'caption'  => 'Weatherstation Info',
                        'rowCount' => 2,
                        'add'      => false,
                        'delete'   => false,
                        'sort'     => [
                            'column'    => 'name',
                            'direction' => 'ascending'],
                        'columns'  => [
                            [
                                'name'    => 'name',
                                'caption' => 'name',
                                'width'   => 'auto',
                                'visible' => true],
                            [
                                'name'    => 'mac',
                                'caption' => 'MAC',
                                'width'   => '150px', ],
                            [
                                'name'    => 'address',
                                'caption' => 'address',
                                'width'   => '150px',
                                'edit'    => [
                                    'type' => 'ValidationTextBox']],
                            [
                                'name'    => 'port',
                                'caption' => 'port',
                                'width'   => '150px']],
                        'values'   => [
                            [
                                'name'    => $this->ReadAttributeString('weatherstation_name'),
                                'mac'     => $this->ReadAttributeString('weatherstation_mac'),
                                'address' => $this->ReadAttributeString('weatherstation_address'),
                                'port'    => $this->ReadAttributeInteger('weatherstation_port')]]]]
            );
        } else {
            $form = array_merge_recursive(
                $form, [
                    [
                        'type'  => 'Label',
                        'label' => 'Find Weatherstation'],
                    [
                        'type'    => 'Button',
                        'label'   => 'Find Weatherstation',
                        'onClick' => 'WeatherStation_FindStation($id, "255.255.255.255", 46000);']]
            );
        }
        $form = array_merge_recursive(
            $form, [
                [
                    'type'  => 'Label',
                    'label' => 'Altitude above sea level for the location of the weatherstation'],
                /*
                     [
                         'type' => 'Label',
                         'label' => 'Altitude from the weather stationabove '.$altidude.' m'
                     ],
                     */ [
                    'name'    => 'altitude_above_sea_level',
                    'type'    => 'NumberSpinner',
                    'caption' => 'altitude (m)'],
                [
                    'type'  => 'Label',
                    'label' => 'Data for Ambient Weather'],
                [
                    'type'  => 'Label',
                    'label' => 'MAC address'],
                [
                    'name'    => 'MAC',
                    'type'    => 'ValidationTextBox',
                    'caption' => 'MAC'],
                [
                    'type'  => 'Label',
                    'label' => 'Ambient Weather Passkey'],
                [
                    'name'    => 'Ambient_Passkey',
                    'type'    => 'ValidationTextBox',
                    'caption' => 'Passkey'],
                [
                    'type'  => 'Label',
                    'label' => 'Wunderground Station ID'],
                [
                    'name'    => 'Wunderground_Station_ID',
                    'type'    => 'ValidationTextBox',
                    'caption' => 'Station ID'],
                [
                    'type'  => 'Label',
                    'label' => 'Wunderground Station Password'],
                [
                    'name'    => 'Wunderground_Station_Password',
                    'type'    => 'ValidationTextBox',
                    'caption' => 'Station Password'],
                [
                    'type'  => 'Label',
                    'label' => 'Weathercloud ID'],
                [
                    'name'    => 'Weathercloud_ID',
                    'type'    => 'ValidationTextBox',
                    'caption' => 'Weathercloud ID'],
                [
                    'type'  => 'Label',
                    'label' => 'Weathercloud Key'],
                [
                    'name'    => 'Weathercloud_Key',
                    'type'    => 'ValidationTextBox',
                    'caption' => 'Weathercloud Key'],
                [
                    'type'  => 'Label',
                    'label' => 'Update Interval Wunderground (seconds)'],
                [
                    'name'    => 'UpdateInterval_Wunderground',
                    'type'    => 'IntervalBox',
                    'caption' => 'Seconds'],
                [
                    'type'  => 'Label',
                    'label' => 'Update Interval Weathercloud (minutes)'],
                [
                    'name'    => 'UpdateInterval_Weathercloud',
                    'type'    => 'IntervalBox',
                    'caption' => 'Minutes'],
                [
                    'type'  => 'Label',
                    'label' => 'Update Interval Weatherbug (minutes)'],
                [
                    'name'    => 'UpdateInterval_Weatherbug',
                    'type'    => 'IntervalBox',
                    'caption' => 'Minutes'],
                [
                    'type'  => 'Label',
                    'label' => 'Update Interval Ambient Weather (seconds)'],
                [
                    'name'    => 'UpdateInterval_AmbientWeather',
                    'type'    => 'IntervalBox',
                    'caption' => 'Seconds'],
                [
                    'type'  => 'Label',
                    'label' => 'Select units:'],
                [
                    'name'    => 'temp_unit',
                    'type'    => 'Select',
                    'caption' => 'Temperature',
                    'options' => [
                        [
                            'label' => 'Celsius °C',
                            'value' => self::Celsius],
                        [
                            'label' => 'Fahrenheit F',
                            'value' => self::Fahrenheit]]],
                [
                    'name'    => 'speed_unit',
                    'type'    => 'Select',
                    'caption' => 'Wind Speed',
                    'options' => [
                        [
                            'label' => 'kmh',
                            'value' => self::kmh],
                        [
                            'label' => 'mph',
                            'value' => self::mph]]],
                [
                    'name'    => 'pressure_unit',
                    'type'    => 'Select',
                    'caption' => 'Air Pressure',
                    'options' => [
                        [
                            'label' => 'pascal',
                            'value' => self::pascal],
                        [
                            'label' => 'bar',
                            'value' => self::bar]]],
                [
                    'name'    => 'rain_unit',
                    'type'    => 'Select',
                    'caption' => 'Rain',
                    'options' => [
                        [
                            'label' => 'mm',
                            'value' => self::mm],
                        [
                            'label' => 'inch',
                            'value' => self::inch]]]]
        );
        return $form;
    }

    /**
     * return form actions.
     *
     * @return array
     */
    protected function FormActions()
    {
        $form = [

        ];

        return $form;
    }

    /*
     * [
                'type' => 'Label',
                'label' => 'Update'
            ],
            [
                'type' => 'Button',
                'label' => 'labelname',
                'onClick' => 'WeatherStation_GetData($id);'
            ]
     */

    /**
     * return from status.
     *
     * @return array
     */
    protected function FormStatus()
    {
        $form = [
            [
                'code'    => 101,
                'icon'    => 'inactive',
                'caption' => 'Creating instance.'],
            [
                'code'    => 102,
                'icon'    => 'active',
                'caption' => 'Device created.'],
            [
                'code'    => 104,
                'icon'    => 'inactive',
                'caption' => 'interface closed.'],
            [
                'code'    => 201,
                'icon'    => 'error',
                'caption' => 'MAC must not be empty']];

        return $form;
    }

    protected function altitude_above_sea_level()
    {
        $location  = $this->getlocation();
        $Latitude  = $location['Latitude'];
        $Longitude = $location['Longitude'];
        $altitude  = $Latitude * $Longitude;
        return $altitude;
    }

    protected function getlocation()
    {
        //Location auslesen
        $LocationID = IPS_GetInstanceListByModuleID('{45E97A63-F870-408A-B259-2933F7EABF74}')[0];
        $ipsversion = $this->GetIPSVersion();
        if ($ipsversion == 5) {
            $Location  = json_decode(IPS_GetProperty($LocationID, 'Location'));
            $Latitude  = $Location->latitude;
            $Longitude = $Location->longitude;
        } else {
            $Latitude  = IPS_GetProperty($LocationID, 'Latitude');
            $Longitude = IPS_GetProperty($LocationID, 'Longitude');
        }
        $location = ['Latitude' => $Latitude, 'Longitude' => $Longitude];
        return $location;
    }

    //Add this Polyfill for IP-Symcon 4.4 and older
    protected function SetValue($Ident, $Value)
    {

        if (IPS_GetKernelVersion() >= 5) {
            parent::SetValue($Ident, $Value);
        } else {
            SetValue($this->GetIDForIdent($Ident), $Value);
        }
    }

    // Readme
    /*
     * Im Anschluss ist ein Account für das Dashboard anzulegen
[Dashboard Ambient Weather](https://dashboard.ambientweather.net/ "Ambient Weather Dashboard")

    Bei IFTTT die Station koppeln
[IFTTT Ambient Weather](https://ifttt.com/ambient_weather "IFTTT Ambient Weather")

Bei Wunderground die Wetterstation anlegen
[Wunderground PWS](https://ifttt.com/ambient_weather "Wunderground PWS")
     */
}
