<?php
function codefairies_bookertools_shows_function($atts){
	extract(shortcode_atts(array('band' => '', 'venue'=>'','limit'=>'', 'city'=>'', 'country'=>'','tour'=>''), $atts));
	
	if($limit==null)$limit='';
	
	if($band!=null && strlen($band)>0){
		$return_string=codefairies_bookertools_return_shows_table(urlencode($band),'band',$limit);
	}else if($venue!=null && strlen($venue)>0){
		$return_string=codefairies_bookertools_return_shows_table(urlencode($venue),'venue',$limit);
	}else if($city!=null && strlen($city)>0){
		$return_string=codefairies_bookertools_return_shows_table(urlencode($city),'city',$limit);
	}else if($country!=null && strlen($country)>0){
		$return_string=codefairies_bookertools_return_shows_table(urlencode($country),'country',$limit);
	}else if($tour!=null && strlen($tour)>0){
		$return_string=codefairies_bookertools_return_shows_table(urlencode($tour),'tour',$limit);
	}else{
		$return_string=codefairies_bookertools_return_shows_table(null,null,$limit);
	}

	return $return_string;
}

function codefairies_bookertools_tours_function($atts){
	extract(shortcode_atts(array('band' => '', 'limit'=>''), $atts));
	
	if($limit==null)$limit='';
	
	if($band!=null && strlen($band)>0){
		$return_string=codefairies_bookertools_return_tours_table(urlencode($band),'band',$limit);
	}else{
		$return_string=codefairies_bookertools_return_tours_table(null,null,$limit);
	}

	return $return_string;
}

//add shortcode
add_shortcode( 'bookertools_shows', 'codefairies_bookertools_shows_function');
add_shortcode( 'bookertools_tours', 'codefairies_bookertools_tours_function');
?>