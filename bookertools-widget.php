<?php
class codefairies_bookertools_widget extends WP_Widget {

	// constructor
	function __construct() {
		  /*parent::__construct(false, $name = __('Bookter Tools Shows', 'wp_widget_plugin') );*/
		  
		  parent::__construct(
 
			// Base ID of your widget
			'bookertools_widget', 
			 
			// Widget name will appear in UI
			__('Bookter Tools Shows', 'wp_widget_plugin'), 
			 
			// Widget description
			array( 'description' => __( 'Widget to display Booker Tools shows', 'wp_widget_plugin' ), )
		);
	}

	// widget form creation
	function form($instance) {	
		// Check values
			if( $instance) {
				$title = esc_attr($instance['title']);
				$show_limit = esc_attr($instance['show_limit']);
			} else {
				$title = '';
				$show_limit='';
			}
?>
			<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'wp_widget_plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			
			<p>
			<label for="<?php echo $this->get_field_id('show_limit'); ?>"><?php _e('Number of shows displayed :', 'wp_widget_plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('show_limit'); ?>" name="<?php echo $this->get_field_name('show_limit'); ?>" type="text" value="<?php echo $show_limit; ?>" />
			</p>
<?php
	}

	// widget update
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		// Fields
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show_limit'] = strip_tags($new_instance['show_limit']);
		return $instance;
	}

	// widget display
	function widget($args, $instance) {
		extract( $args );
		// these are the widget options
		$title = apply_filters('widget_title', $instance['title']);
		$show_limit = $instance['show_limit'];
		$displaystring ='';
		
		$displaystring .= $before_widget;
		// Display the widget
		$displaystring .= '<div class="widget-text wp_widget_plugin_box">';

		// Check if title is set
		if ( $title ) {
			$displaystring .= $before_title . $title . $after_title;
		}
		$displaystring .= codefairies_bookertools_return_shows_ul(null,null,$show_limit);
		$displaystring .= '</div>';
		$displaystring .= $after_widget;
		
		echo $displaystring;
	}

	

}
// Register and load the widget
function codefairies_bookertools_load_widget() {
	register_widget( 'codefairies_bookertools_widget' );
}

// register widget
add_action('widgets_init', 'codefairies_bookertools_load_widget');
?>