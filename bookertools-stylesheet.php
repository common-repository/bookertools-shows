<?php
/**
* Register styles
*/
function enqueue_style(){
	wp_register_style( 'bookertools_css', plugins_url( 'bookertools-integration.css', __FILE__ ));
	wp_enqueue_style('bookertools_css');
}
//set stylesheet
add_action( 'wp_enqueue_scripts', 'enqueue_style');
?>