<?php

require_once("provider.php");

class WeatherProvider implements ServiceProvider {
	// Widget properties
	static $widgetName = "Weather";
	static $widgetIcon = "weather.svg";

	public $location;
	public $width;
	public $height;
	public $ms;

	public function __construct() {
		$this->location = "St Petersburg,RU";
		$this->width = 400;
		$this->height = 200;
		$this->font_size = 0.65;
		$this->font_family = "Arial";
		$this->ms = 'm/s';
	}

    public function getTunables() {
		return array(
			"location"    => array("type" => "text", "display" => "Location", "value" => $this->location),
			"font_family" => array("type" => "text", "display" => "Font Family", "value" => $this->font_family),
			"font_size"   => array("type" => "fnum", "display" => "Font Size", "value" => $this->font_size),
			"ms"   => array("type" => "text", "display" => "Wind speed measurement unit", "value" => $this->ms)
		);
	}
    public function setTunables($v) {
		$this->location = $v["location"]["value"];
		$this->font_family = $v["font_family"]["value"];
		$this->font_size = $v["font_size"]["value"];
		$this->ms = $v['ms']['value'];
	}

    public function shape() {
		// Return default width/height
		return array(
			"width"       => $this->width,
			"height"      => $this->height,
			"resizable"   => true,
			"keep_aspect" => true,
		);
    }

    public function render() {
		// Gather information from OpenWeatherMap
		$raw = file_get_contents("http://api.openweathermap.org/data/2.5/weather?q=".urlencode($this->location)."&APPID=".GlobalConfig::$weather_api_key);
		$weather = json_decode($raw, true);

		$icon = $weather["weather"][0]["icon"];
		$temp = $weather["main"]["temp"] - 273.15;

		// Generate an SVG image out of this
		return sprintf(
			'<svg width="%d" height="%d" version="1.1" xmlns="http://www.w3.org/2000/svg"
				xmlns:xlink="http://www.w3.org/1999/xlink">
				<image x="%d" y="%d" width="%d" height="%d" xlink:href="%s" />
                <text text-anchor="end" x="%d" y="%d" fill="black" style="font-size: %dpx; font-family: %s; font-weight: bold;">%d°</text>
                <image x="%d" y="%d" width="%d" height="%d" xlink:href="%s" transform="rotate(%d %d %d)" />
                <text text-anchor="start" x="%d" y="%d" fill="black" style="font-size: %dpx; font-family: %s; font-weight: bold;">%s%s</text>
			</svg>', $this->width, $this->height,
				0.03 * $this->width, 0.05 * $this->height,
				0.45 * $this->width, 0.9 * $this->height,
				ProviderAux::embedSVG("resources/".$this->imgmap[$icon].".svg"),

				// temp
				$this->width, ($this->font_size/3.0 + 0.3) * $this->height, $this->font_size * $this->height,
				$this->font_family,
				round($temp),

				// wind icon
				0.53 * $this->width, ($this->font_size/3.0 + 0.4) * $this->height,
				0.1 * $this->width, 0.25 * $this->height,
				ProviderAux::embedSVG("resources/right-arrow.svg"), round($weather['wind']['deg'] - 90),
				0.58 * $this->width, ($this->font_size/3.0 + 0.525) * $this->height, // icon rotation

				// wind
				0.65 * $this->width, ($this->font_size/3.0 + 0.6) * $this->height, $this->font_size * $this->height / 2.5,
				$this->font_family,
				round($weather['wind']['speed']), $this->ms
		);
	}

	// Weather code -> image mapping!
	public $imgmap = array(
		"01d" => "sun",
		"01n" => "moon",
		"02d" => "sun_cloud",
		"02n" => "moon_cloud",
		"03d" => "sun_scat_cloud",
		"03n" => "moon_scat_cloud",
		"04d" => "clouds",
		"04n" => "clouds",
		"09d" => "shower_rain",
		"09n" => "shower_rain",
		"10d" => "light_rain",
		"10n" => "light_rain",
		"11d" => "thunder",
		"11n" => "thunder",
		"13d" => "snow",
		"13n" => "snow",
		"50d" => "day_mist",
		"50n" => "night_mist",
	);
};

?>
