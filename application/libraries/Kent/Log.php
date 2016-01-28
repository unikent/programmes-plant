<?php namespace Kent;

class Log {

	public static $logfile;

	private static function put($msg,$write=true,$echo=false, $type=''){

		if($write) {
			$data =  date('Y-m-d H:i:s') . ":- " . $type . ' ' . $msg . "\r\n";
			file_put_contents(self::$logfile, $data,FILE_APPEND);
		}
		if($echo){
			echo $msg . "\r\n";
		}
	}


	public static function warn($msg){
		self::put($msg,true,false,'WARN');
	}

	public static function error($msg){
		self::put($msg,true,true,'ERROR');
	}

	public static function silenterror($msg){
		self::put($msg,true,false,'ERROR');
	}

	public static function info($msg){
		self::put($msg,false,true);
	}

	public static function purge(){
		try{
			@unlink(self::$logfile);
		}catch (\Exception $x){
			echo 'Failed to purge log file at: ' . self::$logfile;
		}
	}
}

// Set user-defined error handler function
set_error_handler(function($errno, $errstr, $errfile, $errline) {

	if (error_reporting() == 0) {
		return;
	}

	if($errstr !=='SimpleXMLElement::asXML(): string is not in UTF-8') {
		Log::warn("$errfile:$errline -> ($errno) $errstr ");
	}
});