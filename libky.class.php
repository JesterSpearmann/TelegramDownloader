<?php
/* Class implementing standard features commonly used by the author. */
/* State: 2016-03-04 */

abstract class LibKy {
	public static $phases = array("|", "/", "-", "\\");
	public static $currPhase = array();
	
	public static function log ($msg) {
		echo "[" . getmypid() . "][" . date("Y-m-d-His") . "] " . $msg . "\n";
	}
	
	public static function logNoEOL ($msg) {
		echo "[" . getmypid() . "][" . date("Y-m-d-His") . "] " . $msg;
	}
	
	public static function nlog () {
		echo "\n";
	}
	
	public static function spin() {
		if(empty(self::$currPhase)) self::$currPhase = self::$phases;
		printf('%s%s', chr(8), array_shift(self::$currPhase));
	}
}