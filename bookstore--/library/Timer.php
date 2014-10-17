<?php 
class Timer {
	static function DatetimeInGMT($time = null) {
		if ($time == null) {
			$time = time ();
		}
		return date ( "Y-m-d H:i:s", $time - date ( "Z", $time ) );
	}

	static function GMTDatetimeToLocal($datetime) {

		if (! is_numeric ( $datetime )) {
			$time = strtotime ( $datetime );
		} else {
			$time = ($datetime);
		}

		return date ( "Y-m-d H:i:s", $time + date ( "Z", $time ) );
	}
}