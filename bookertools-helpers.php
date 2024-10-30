<?php

function codefairies_bookertools_searchForHash($array) {
   foreach ($array as $key => $val) {
       if (isset($val['team_hash'])) {
           return $key;
       }
   }
   return null;
}

function codefairies_bookertools_debug_to_console( $data ) {
	if ( is_array( $data ) )
		$output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
	else
		$output = "<script>console.log( 'Debug Objects: " . json_encode( $data) . "' );</script>";
	echo $output;
}

function codefairies_bookertools_log_to_bookertools(string $message){
	$message = 'Error in bookertools plugin: ' . $message;
	error_log($message,1,'info@bookertools.com');
}

function codefairies_bookertools_parseIso8601date($date){
	if(strlen($date)>0){
	 	//$d = DateTime::createFromFormat(DateTime::ISO8601, $date);
		 $d = DateTime::createFromFormat("Y-m-d\TH:i:s", $date);
	 	return $d->format('d-m-Y');
	}
}

?>