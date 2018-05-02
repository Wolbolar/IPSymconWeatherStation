<?

class WeatherStation extends IPSModule
{
	// helper properties
	private $position = 0;

	public function Create()
	{
		//Never delete this line!
		parent::Create();

		//These lines are parsed on Symcon Startup or Instance creation
		//You cannot use variables here. Just static values.

		$this->RequireParent("{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}");

		$this->RegisterPropertyString("ApiKey", "");
		$this->RegisterPropertyString("ApplicationKey", "ac14c2f0d58541d0a4714e51d97785e95728ce6ade5743dc8bec238fcc2c715b"); // API_Development_Key
		$this->RegisterPropertyString("MAC", "");
		$this->RegisterPropertyInteger('temp_unit', 1);
		$this->RegisterPropertyInteger('speed_unit', 1);
		$this->RegisterPropertyInteger('pressure_unit', 1);
		$this->RegisterPropertyInteger('rain_unit', 1);
	}

	public function ApplyChanges()
	{
		//Never delete this line!
		parent::ApplyChanges();

		$temp_unit = $this->ReadPropertyInteger("temp_unit");
		$speed_unit = $this->ReadPropertyInteger("speed_unit");
		$pressure_unit = $this->ReadPropertyInteger("pressure_unit");

		if($temp_unit == 1)
		{
			$this->RegisterVariableFloat("Indoor_Temp", $this->Translate("Indoor Temperature"), "~Temperature", $this->_getPosition());
			$this->RegisterVariableFloat("Outdoor_Temp", $this->Translate("Outdoor Temperature"), "~Temperature", $this->_getPosition());
			$this->RegisterVariableFloat("Windchill", $this->Translate("Windchill"), "~Temperature", $this->_getPosition());
			$this->RegisterVariableFloat("Dewpoint", $this->Translate("Dewpoint"), "~Temperature", $this->_getPosition());
		}
		else
		{
			$this->RegisterVariableFloat("Indoor_Temp", $this->Translate("Indoor Temperature"), "~Temperature.Fahrenheit", $this->_getPosition());
			$this->RegisterVariableFloat("Outdoor_Temp", $this->Translate("Outdoor Temperature"), "~Temperature.Fahrenheit", $this->_getPosition());
			$this->RegisterVariableFloat("Windchill", $this->Translate("Windchill"), "~Temperature.Fahrenheit", $this->_getPosition());
			$this->RegisterVariableFloat("Dewpoint", $this->Translate("Dewpoint"), "~Temperature.Fahrenheit", $this->_getPosition());
		}



		$this->RegisterVariableFloat("Indoor_Humidity", $this->Translate("Indoor Humidity"), "~Humidity.F", $this->_getPosition());
		$this->RegisterVariableFloat("Outdoor_Humidity", $this->Translate("Outdoor Humidity"), "~Humidity.F", $this->_getPosition());
		if($speed_unit == 1)
		{
			$this->RegisterVariableFloat("Windspeed_km", $this->Translate("Windspeed"), "~WindSpeed.kmh", $this->_getPosition());
			$this->RegisterVariableFloat("Windspeed_ms", $this->Translate("Windspeed"), "~WindSpeed.ms", $this->_getPosition());
			$this->RegisterVariableFloat("Windgust", $this->Translate("Wind gust"), "~WindSpeed.ms", $this->_getPosition());
		}
		else
		{
			$this->RegisterVariableFloat("Windspeed_km", $this->Translate("Windspeed"), "~WindSpeed.kmh", $this->_getPosition());
			$this->RegisterVariableFloat("Windspeed_ms", $this->Translate("Windspeed"), "~WindSpeed.ms", $this->_getPosition());
			$this->RegisterVariableFloat("Windgust", $this->Translate("Windgust"), "~WindSpeed.ms", $this->_getPosition());
		}


		$this->RegisterVariableInteger("Wind_Direction", $this->Translate("Wind Direction"), "~WindDirection", $this->_getPosition());

		if($pressure_unit == 1)
		{
			$this->RegisterVariableFloat("absbaromin", $this->Translate("Air Pressure absolut"), "~AirPressure.F", $this->_getPosition());
			$this->RegisterVariableFloat("baromin", $this->Translate("Air Pressure"), "~AirPressure.F", $this->_getPosition());
		}
		else
		{
			$this->RegisterVariableFloat("absbaromin", $this->Translate("Air Pressure absolut"), "~AirPressure.F", $this->_getPosition());
			$this->RegisterVariableFloat("baromin", $this->Translate("Air Pressure"), "~AirPressure.F", $this->_getPosition());
		}

		$this->RegisterVariableFloat("rainin", $this->Translate("Rain"), "~Rainfall", $this->_getPosition());
		$this->RegisterVariableFloat("dailyrainin", $this->Translate("Daily Rain"), "~Rainfall", $this->_getPosition());
		$this->RegisterVariableFloat("weeklyrainin", $this->Translate("Weekly Rain"), "~Rainfall", $this->_getPosition());
		$this->RegisterVariableFloat("monthlyrainin", $this->Translate("Monthly Rain"), "~Rainfall", $this->_getPosition());
		$this->RegisterVariableFloat("solarradiation", $this->Translate("Solar Radiation"), "", $this->_getPosition());
		$this->RegisterVariableInteger("UV", $this->Translate("UV"), "", $this->_getPosition());
		$this->RegisterVariableString("Date", $this->Translate("Date"), "", $this->_getPosition());
		$this->RegisterVariableString("Software_Type", $this->Translate("Software Type"), "", $this->_getPosition());
		$this->RegisterVariableString("Action", $this->Translate("Action"), "", $this->_getPosition());
		$this->RegisterVariableInteger("Realtime", $this->Translate("Realtime"), "", $this->_getPosition());
		$this->RegisterVariableInteger("Frequence", $this->Translate("Frequence"), "", $this->_getPosition());



		$this->ValidateConfiguration();

	}

	/**
	 * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
	 * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wie folgt zur Verfügung gestellt:
	 */

	private function ValidateConfiguration()
	{
		$mac = $this->ReadPropertyString('MAC');
		if($mac == "")
		{
			$this->SetStatus(201);
		}
		else{
			$this->SetStatus(102);
		}
	}

	public function GetData()
	{

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

	protected function MPHToMS(float $mph)
	{
		$ms = $mph * 0.44704;
		return $ms;
	}

	protected function Pressure(float $pressure)
	{
		$pascal = $pressure / 0.02952998751;
		return $pascal;
	}

	protected function SendToIO(string $payload)
	{
		$result = $this->SendDataToParent(json_encode(Array("DataID" => "{C8792760-65CF-4C53-B5C7-A30FCC84FEFE}", "Buffer" => $payload))); // TX Server Socket
		return $result;
	}

	public function ReceiveData($JSONString) {

		$this->SendDebug("Weatherstation:", $JSONString, 0);
		$payload = json_decode($JSONString);
		$type = $payload->Type;
		if($type == 0)
		{
			$this->SendDebug("Weatherstation:", json_encode($payload->Buffer), 0);
			$this->WriteData($payload->Buffer);
		}
	}

	protected function WriteData($payloadraw)
	{
		$payload = substr($payloadraw,4,strlen($payloadraw)-4);
		$url = "http://192.168.1.1/".$payload;
		$this->SendDebug("Weatherstation:", $url, 0);
		$query = parse_url($url, PHP_URL_QUERY);
		parse_str($query, $data);
		$temp_unit = $this->ReadPropertyInteger("temp_unit");
		$speed_unit = $this->ReadPropertyInteger("speed_unit");
		$pressure_unit = $this->ReadPropertyInteger("pressure_unit");
		$indoor_temperature = $data["indoortempf"];
		$this->SendDebug("Weatherstation:", "indoor temperature: " . $indoor_temperature, 0);
		$temperature = $data["tempf"];
		$this->SendDebug("Weatherstation:", "temperature: " . $temperature, 0);
		$dewpoint = $data["dewptf"];
		$this->SendDebug("Weatherstation:", "dewpoint: " . $dewpoint, 0);
		$windchill = $data["windchillf"];
		$this->SendDebug("Weatherstation:", "windchill: " . $windchill, 0);
		if($temp_unit == 1)
		{
			$this->SetValue("Indoor_Temp", $this->FahrenheitToCelsius($indoor_temperature));
			$this->SetValue("Outdoor_Temp", $this->FahrenheitToCelsius($temperature));
			$this->SetValue("Windchill", $this->FahrenheitToCelsius($dewpoint));
			$this->SetValue("Dewpoint", $this->FahrenheitToCelsius($windchill));
		}
		else
		{
			$this->SetValue("Indoor_Temp", $indoor_temperature);
			$this->SetValue("Outdoor_Temp", $temperature);
			$this->SetValue("Windchill", $dewpoint);
			$this->SetValue("Dewpoint", $windchill);
		}
		$indoorhumidity = $data["indoorhumidity"];
		$this->SendDebug("Weatherstation:", "indoor humidity: " . $indoorhumidity, 0);
		$humidity = $data["humidity"];
		$this->SendDebug("Weatherstation:", "windchill: " . $humidity, 0);
		$this->SetValue("Indoor_Humidity", $indoorhumidity);
		$this->SetValue("Outdoor_Humidity", $humidity);
		$windspeed = $data["windspeedmph"];
		$this->SendDebug("Weatherstation:", "windspeed: " . $windspeed, 0);
		$windgust = $data["windgustmph"];
		$this->SendDebug("Weatherstation:", "windgust: " . $windgust, 0);
		if($speed_unit == 1)
		{
			$this->SetValue("Windspeed_km", $this->MilesToKilometer($windgust));
			$this->SetValue("Windspeed_ms", $this->MPHToMS($windspeed));
			$this->SetValue("Windgust", $this->MilesToKilometer($windgust));
		}
		else
		{
			$this->SetValue("Windspeed_km", $windgust);
			$this->SetValue("Windspeed_ms", $this->MPHToMS($windspeed));
			$this->SetValue("Windgust", $windgust);
		}
		$winddir = $data["winddir"];
		$this->SendDebug("Weatherstation:", "wind direction: " . $winddir, 0);
		$this->SetValue("Wind_Direction", $winddir);
		$absbaromin = $data["absbaromin"];
		$this->SendDebug("Weatherstation:", "barometer min: " . $absbaromin, 0);
		$baromin = $data["baromin"];
		$this->SendDebug("Weatherstation:", "abs barometer min: " . $baromin, 0);

		if($pressure_unit == 1)
		{
			$this->SetValue("absbaromin", $this->Pressure($absbaromin));
			$this->SetValue("baromin", $this->Pressure($baromin));
		}
		else
		{
			$this->SetValue("absbaromin", $absbaromin);
			$this->SetValue("baromin", $baromin);
		}
		$rainin = $data["rainin"];
		$this->SendDebug("Weatherstation:", "rain: " . $rainin, 0);
		$dailyrainin = $data["dailyrainin"];
		$this->SendDebug("Weatherstation:", "daily rain: " . $dailyrainin, 0);
		$weeklyrainin = $data["weeklyrainin"];
		$this->SendDebug("Weatherstation:", "weekly rain: " . $weeklyrainin, 0);
		$monthlyrainin = $data["monthlyrainin"];
		$this->SendDebug("Weatherstation:", "monthly rain: " . $monthlyrainin, 0);
		$solarradiation = $data["solarradiation"];
		$this->SendDebug("Weatherstation:", "solar radiation: " . $solarradiation, 0);
		$uv = $data["UV"];
		$this->SendDebug("Weatherstation:", "uv: " . $uv, 0);
		$dateutc = $data["dateutc"];
		$this->SendDebug("Weatherstation:", "date utc: " . $dateutc, 0);
		$softwaretype = $data["softwaretype"];
		$this->SendDebug("Weatherstation:", "software type: " . $softwaretype, 0);
		$action = $data["action"];
		$this->SendDebug("Weatherstation:", "action: " . $action, 0);
		$realtime = $data["realtime"];
		$this->SendDebug("Weatherstation:", "realtime: " . $realtime, 0);
		$rtfreq = $data["rtfreq"];
		$this->SendDebug("Weatherstation:", "rt freq: " . $rtfreq, 0);

		$this->SetValue("rainin", $rainin);
		$this->SetValue("dailyrainin", $dailyrainin);
		$this->SetValue("weeklyrainin", $weeklyrainin);
		$this->SetValue("monthlyrainin", $monthlyrainin);
		$this->SetValue("solarradiation", $solarradiation);
		$this->SetValue("UV", $uv);
		$this->SetValue("Date", $dateutc);
		$this->SetValue("Software_Type", $softwaretype);
		$this->SetValue("Action", $action);
		$this->SetValue("Realtime", $realtime);
		$this->SetValue("Frequence", $rtfreq);
	}

	/**
	 * gets current IP-Symcon version
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
			if ($profile['ProfileType'] != $Vartype)
				$this->SendDebug("Weatherstation:", "Variable profile type does not match for profile " . $Name, 0);
		}

		IPS_SetVariableProfileIcon($Name, $Icon);
		IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
		IPS_SetVariableProfileDigits($Name, $Digits); //  Nachkommastellen
		IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize); // string $ProfilName, float $Minimalwert, float $Maximalwert, float $Schrittweite
	}

	protected function RegisterProfileAssociation($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Vartype, $Associations)
	{
		if (sizeof($Associations) === 0) {
			$MinValue = 0;
			$MaxValue = 0;
		}

		$this->RegisterProfile($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Vartype);

		//boolean IPS_SetVariableProfileAssociation ( string $ProfilName, float $Wert, string $Name, string $Icon, integer $Farbe )
		foreach ($Associations as $Association) {
			IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
		}

	}

	/**
	 * return incremented position
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
	 * build configuration form
	 * @return string
	 */
	public function GetConfigurationForm()
	{
		// return current form
		return json_encode([
			'elements' => $this->FormHead(),
			'actions' => $this->FormActions(),
			'status' => $this->FormStatus()
		]);
	}

	/**
	 * return form configurations on configuration step
	 * @return array
	 */
	protected function FormHead()
	{
		$form = [
			[
				'type' => 'Label',
				'label' => 'MAC adress'
			],
			[
				'name' => 'MAC',
				'type' => 'ValidationTextBox',
				'caption' => 'MAC'
			]
		];
		return $form;
	}

	/**
	 * return form actions
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
	 * return from status
	 * @return array
	 */
	protected function FormStatus()
	{
		$form = [
			[
				'code' => 101,
				'icon' => 'inactive',
				'caption' => 'Creating instance.'
			],
			[
				'code' => 102,
				'icon' => 'active',
				'caption' => 'Device created.'
			],
			[
				'code' => 104,
				'icon' => 'inactive',
				'caption' => 'interface closed.'
			],
			[
				'code' => 201,
				'icon' => 'error',
				'caption' => 'MAC must not be empty'
			]
		];

		return $form;
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

?>