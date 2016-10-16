<?php

require_once("provider.php");

class DatetimeProvider implements ServiceProvider {
	// Widget properties
	static $widgetName = "Date/time";
	static $widgetIcon = "datetime.svg";

	public $timezone;
	public $locale;
	public $format;
	public $width;
	public $height;

	public function __construct() {
		$this->timezone = 'Europe/Moscow';
		$this->format = '%c';
		$this->width = 1000;
		$this->height = 80;
		$this->font_size = 0.65;
		$this->font_family = "Arial";
	}

    public function getTunables() {
		return array(
			"timezone"    => array("type" => "text", "display" => "Timezone", "value" => $this->timezone),
			"format"      => array("type" => "text", "display" => "Format", "value" => $this->format),
			"font_family" => array("type" => "text", "display" => "Font Family", "value" => $this->font_family),
			"font_size"   => array("type" => "fnum", "display" => "Font Size", "value" => $this->font_size)
		);
	}
    public function setTunables($v) {
		$this->timezone = $v["timezone"]["value"];
		$this->format = $v["format"]["value"];
		$this->font_family = $v["font_family"]["value"];
		$this->font_size = $v["font_size"]["value"];
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
		// Generate an SVG image out of this
		$tz = date_default_timezone_get();
		date_default_timezone_set($this->timezone);
		$datetime = strftime($this->format);
		date_default_timezone_set($tz);
		return sprintf(
			'<svg width="%d" height="%d" version="1.1" xmlns="http://www.w3.org/2000/svg"
				xmlns:xlink="http://www.w3.org/1999/xlink">
                <text text-anchor="middle" x="50%%" y="60%%" fill="black" style="font-size: %dpx; font-family: %s;">
					%s
				</text>
			</svg>', $this->width, $this->height,
			    $this->font_size * $this->height, $this->font_family,
				$datetime
		);
	}
};

?>
