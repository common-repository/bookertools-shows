<?php
/**
 * Plugin Name: Booker Tools Integration
 * Plugin URI: https://app.bookertools.com
 * Description: This plugin offers integration with Booker Tools through a widget and shortcodes [bookertools_shows] & [bookertools_tours] to display your announced Booker Tools shows and tours.
 * Version: 1.5.2
 * Author: CodeFairies
 * Author URI: https://www.codefairies.com
 * License: GPL2
 */

 // include helper classes keep this order
 include 'bookertools-helpers.php';
 include 'bookertools-service.php';
 include 'bookertools-htmlreturn.php';
 // add widget support
 include 'bookertools-widget.php';
 // add shortcode support
 include 'bookertools-shortcode.php';
 // add stylesheet
  include 'bookertools-stylesheet.php';

//add menu
if( is_admin() )
    $my_settings_page = new codefairies_bookertools_settings();


class codefairies_bookertools_settings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'codefairies_bookertools_add_plugin_page' ) ); //toevoegen aan sidebar menu
        add_action( 'admin_init', array( $this, 'init_bookertools_link' ) );
		add_action( 'admin_init', array( $this, 'init_bookertools_options' ) );
    }

    /**
     * Add options page
     */
    public function codefairies_bookertools_add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Booker Tools Settings', //page title
            'Booker Tools',          //menu title
            'manage_options', 		//required userrights to see page
            'bookertools-settings', //unique slug name
            array( $this, 'codefairies_bookertools_create_admin_page' )
        );

    }

    /**
     * Options page callback
     */
    public function codefairies_bookertools_create_admin_page()
    {
        // get properties from database
        $this->options = get_option('bookertools-hash' );
		$this->options_shortcode=get_option('bookertools-shortcode-option');
		
		//https://tommcfarlin.com/multiple-sections-on-wordpress-options-pages/
		//https://code.tutsplus.com/tutorials/the-wordpress-settings-api-part-5-tabbed-navigation-for-settings--wp-24971
        ?>
        <div class="wrap">
            <h2>Booker Tools Settings</h2>  
			
			<?php
			$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'bookertools_link';
			?>			
			<h2 class="nav-tab-wrapper">
				<a href="?page=bookertools-settings&tab=bookertools_link"  class="nav-tab <?php echo $active_tab == 'bookertools_link' ? 'nav-tab-active' : ''; ?>">Token Validation</a>
				<a href="?page=bookertools-settings&tab=shortcode_options" class="nav-tab <?php echo $active_tab == 'shortcode_options' ? 'nav-tab-active' : ''; ?>">Shortcode Options</a>
			</h2>
			
			<?php if( $active_tab == 'bookertools_link' ) { ?>
				<h3>Link your Booker Tools account to your Wordpress website (more information <a href="https://www.booker.tools/knowledgebase/bookertools-integration-wordpress-plug/" target="_blank">here</a>)</h3>     
			<?php } else if ($active_tab == 'shortcode_options' ) { ?>
				<h3>Customise shortcode output</h3>
			<?php } ?>
			
            <form method="post" action="options.php">
            <?php
				if( $active_tab == 'bookertools_link' ) { 
					// This prints out all hidden setting fields
					settings_fields( 'bookertools-option-group' );   
					do_settings_sections( 'bookertools-settings' ); //page
					submit_button('Get Token'); 
				}else if ($active_tab=='shortcode_options'){
					// This prints out all hidden setting fields
					settings_fields( 'bookertools-shortcode-option' );   
					do_settings_sections( 'bookertools-shortcode-option' ); //page
					submit_button('Save'); 
				}
            ?>
            </form>

        </div>
        <?php
    }

	

    /**
     * Register and add settings
     */
    public function init_bookertools_link()
    {        
		//debug_to_console("page init");

        add_settings_section(
            'bookertools_section_url', // ID
            '', // Title
            array( $this, 'codefairies_bookertools_print_section_info_url' ), // Callback
            'bookertools-settings' // Page
        );  

        add_settings_field(
            'team_url', 
            'Booker Tools url', 
            array( $this, 'codefairies_bookertools_url_callback' ), 
            'bookertools-settings', 
            'bookertools_section_url'
        );      
        
        add_settings_section(
            'bookertools_section_key', // ID
            '', // Title
            array( $this, 'codefairies_bookertools_print_section_info_key' ), // Callback
            'bookertools-settings' // Page
        );  

		add_settings_field(
            'team_hash', 
            'Unique key', 
            array( $this, 'codefairies_bookertools_key_callback' ), 
            'bookertools-settings', 
            'bookertools_section_key'
        );  

        add_settings_section(
            'bookertools_section_token', // ID
            '', // Title
            array( $this, 'codefairies_bookertools_print_section_info_token' ), // Callback
            'bookertools-settings' // Page
        ); 

        add_settings_field(
            'team_has_token', 
            'Key validated successfully', 
            array( $this, 'codefairies_bookertools_token_callback' ), 
            'bookertools-settings', 
            'bookertools_section_token'
        ); 

		register_setting(
            'bookertools-option-group', // Option group
            'bookertools-hash', // Option name
            array( $this, 'codefairies_bookertools_sanitize' ) // Sanitize
        );
    }
	
	public function init_bookertools_options()
    {        
		// shortcode settings
		
		add_settings_section(
            'bookertools_section_options', // ID
            '', // Title
            array( $this, 'codefairies_bookertools_print_section_noshowsfound' ), // Callback
            'bookertools-shortcode-option' // Page
        ); 

        add_settings_field(
            'team_noshowsfound',  //ID
            '"No shows found" text',  //Title
            array( $this, 'codefairies_bookertools_noshowsfound_callback' ), //callback
            'bookertools-shortcode-option', //page
            'bookertools_section_options' //section
        ); 
		
		add_settings_field(
            'team_notoursfound',  //ID
            '"No tours found" text',  //Title
            array( $this, 'codefairies_bookertools_notoursfound_callback' ), //callback
            'bookertools-shortcode-option', //page
            'bookertools_section_options' //section
        ); 
		
		add_settings_field(
            'team_showtourname', 
            'Show tourname if available', 
            array( $this, 'codefairies_bookertools_showtourname_callback' ), 
            'bookertools-shortcode-option', 
            'bookertools_section_options'
        ); 
		
		add_settings_field(
            'team_groupshowssamedate', 
            'Group shows on same date', 
            array( $this, 'codefairies_bookertools_groupshowssamedate_callback' ), 
            'bookertools-shortcode-option', 
            'bookertools_section_options'
        ); 

		add_settings_field(
            'team_showticketlink', 
            'Show ticket link if available', 
            array( $this, 'codefairies_bookertools_showticketlink_callback' ), 
            'bookertools-shortcode-option', 
            'bookertools_section_options'
        ); 

		add_settings_field(
            'team_showfacebooklink', 
            'Show Facebook event link if available', 
            array( $this, 'codefairies_bookertools_showfacebooklink_callback' ), 
            'bookertools-shortcode-option', 
            'bookertools_section_options'
        ); 
		
		register_setting(
             'bookertools-shortcode-option', // Option group
             'bookertools-shortcode-option', // Option name
             array( $this, 'codefairies_bookertools_sanitize2' ) // Sanitize
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function codefairies_bookertools_sanitize( $input )
    {
        $new_input = array();
        
        if( isset( $input['team_url'] ) )
            $new_input['team_url'] = sanitize_text_field( $input['team_url'] );

        if( isset( $input['team_hash'] ) )
            $new_input['team_hash'] = sanitize_text_field( $input['team_hash'] );

        // set the has token to 0 as a default
        $new_input['team_has_token'] = '0';
        $new_input['team_token'] = '';
        
        $token_url = $new_input['team_url'] . '/api/account/token/' . $new_input['team_hash'];
        $response = wp_remote_get($token_url);
		
		
        if(is_wp_error($response)){
            // wordpress was not able to process this request
            codefairies_bookertools_log_to_bookertools($response->get_error_message());
            $new_input['team_hash'] = '';
            $this->codefairies_bookertools_add_settings_error($response->get_error_message());
        }else{
            $http_code = wp_remote_retrieve_response_code( $response );
            if($http_code == 200){
                // bookertools created a jwt token
                $body = json_decode(wp_remote_retrieve_body( $response ));
				
                if(isset($body->{'token'})){
                    $new_input['team_token'] = sanitize_text_field( $body->{'token'} );
                    $new_input['team_has_token'] = '1'; 
                }else{
                    $new_input['team_hash'] = '';
                    $this->codefairies_bookertools_add_settings_error('Booker Tools can\'t validate key');
                }

            }elseif($http_code == 403){
                // bookertools can not create a token for this team hash
                $new_input['team_hash'] = '';
            }else{
                // an unexpected return status is send back
                codefairies_bookertools_log_to_bookertools('Booker Tools can\'t create a token for ' . $token_url);
                $new_input['team_hash'] = '';
                $this->codefairies_bookertools_add_settings_error('Booker Tools can\'t can validate key');
            }
        }
        return $new_input;
    }

    private function codefairies_bookertools_add_settings_error($message){
            add_settings_error(
                'bookertools-hash',
                esc_attr( 'settings_updated' ),
                $message,
                'error'
            );
    }

    /** 
     * Print the Section text
     */
    public function codefairies_bookertools_print_section_info_url()
    {
        print '';
    }

    public function codefairies_bookertools_url_callback(){
        printf(
            '
            <input type="url" style="width:100%%" id="team_url" name="bookertools-hash[team_url]" value="%s" placeholder="bookertools url" />
            ',
            isset( $this->options['team_url'] ) ? esc_attr( $this->options['team_url']) : 'https://api.bookertools.com'
        );
    }

    /** 
     * Print the Section text
     */
    public function codefairies_bookertools_print_section_info_key()
    {
        print '';
    }

    public function codefairies_bookertools_key_callback(){
        printf(
            '
            <input type="text" style="width:100%%" id="team_hash" name="bookertools-hash[team_hash]" value="%s" placeholder="application public key" />
            ',
            isset( $this->options['team_hash'] ) ? esc_attr( $this->options['team_hash']) : ''
        );
    }

    /** 
     * Print the Section text
     */
    public function codefairies_bookertools_print_section_info_token()
    {
        print '';
    }

    public function codefairies_bookertools_token_callback(){
        printf(
            '
            <input type="checkbox" disabled readonly id="team_has_token" name="bookertools-hash[team_has_token]" %s > <br>
            ',
            isset( $this->options['team_has_token'] ) ? esc_attr( ($this->options['team_has_token'] == '1' ? 'checked' : '')) : ''
        );
    }
	/* -------------- */
	/* no shows found */
	/* -------------- */
	public function codefairies_bookertools_sanitize2( $input )
    {
        $new_input = array();
        
        if( isset( $input['team_noshowsfound'] ) ){
            $new_input['team_noshowsfound'] = sanitize_text_field( $input['team_noshowsfound'] );
		}
		
		if( isset( $input['team_notoursfound'] ) ){
            $new_input['team_notoursfound'] = sanitize_text_field( $input['team_notoursfound'] );
		}
	
		if (isset ($input['team_showtourname'])){
			$new_input['team_showtourname']=$input['team_showtourname'];
		}
		
		if(isset($input['team_groupshowssamedate'])){
			$new_input['team_groupshowssamedate']=$input['team_groupshowssamedate'];
		}

		if (isset ($input['team_showticketlink'])){
			$new_input['team_showticketlink']=$input['team_showticketlink'];
		}

		if (isset ($input['team_showfacebooklink'])){
			$new_input['team_showfacebooklink']=$input['team_showfacebooklink'];
		}
	
        return $new_input;
    }
	
    public function codefairies_bookertools_print_section_noshowsfound()
    {
        print '';
    }

    public function codefairies_bookertools_noshowsfound_callback(){
        printf(
            '
            <input type="text" style="width:100%%" id="team_noshowsfound" name="bookertools-shortcode-option[team_noshowsfound]" value="%s" placeholder="No shows found" > <br>
            ',
            isset( $this->options_shortcode['team_noshowsfound'] ) ? esc_attr( $this->options_shortcode['team_noshowsfound']) : 'No shows found'
        );
    }
	
	public function codefairies_bookertools_notoursfound_callback(){
        printf(
            '
            <input type="text" style="width:100%%" id="team_notoursfound" name="bookertools-shortcode-option[team_notoursfound]" value="%s" placeholder="No tours found" > <br>
            ',
            isset( $this->options_shortcode['team_notoursfound'] ) ? esc_attr( $this->options_shortcode['team_notoursfound']) : 'No tours found'
        );
    }
	
	public function codefairies_bookertools_showtourname_callback(){
         printf(
            '
            <input type="checkbox" id="team_showtourname" name="bookertools-shortcode-option[team_showtourname]" %s > <br>
            ',
            isset( $this->options_shortcode['team_showtourname'] ) ? esc_attr( ($this->options_shortcode['team_showtourname'] == 'on' ? 'checked' : '')) : ''
        );
    }
	
	public function codefairies_bookertools_groupshowssamedate_callback(){
         printf(
            '
            <input type="checkbox" id="team_groupshowssamedate" name="bookertools-shortcode-option[team_groupshowssamedate]" %s > <br>
            ',
            isset( $this->options_shortcode['team_groupshowssamedate'] ) ? esc_attr( ($this->options_shortcode['team_groupshowssamedate'] == 'on' ? 'checked' : '')) : ''
        );
    }

	public function codefairies_bookertools_showticketlink_callback(){
         printf(
            '
            <input type="checkbox" id="team_showticketlink" name="bookertools-shortcode-option[team_showticketlink]" %s > <br>
            ',
            isset( $this->options_shortcode['team_showticketlink'] ) ? esc_attr( ($this->options_shortcode['team_showticketlink'] == 'on' ? 'checked' : '')) : ''
        );
    }

	public function codefairies_bookertools_showfacebooklink_callback(){
         printf(
            '
            <input type="checkbox" id="team_showfacebooklink" name="bookertools-shortcode-option[team_showfacebooklink]" %s > <br>
            ',
            isset( $this->options_shortcode['team_showfacebooklink'] ) ? esc_attr( ($this->options_shortcode['team_showfacebooklink'] == 'on' ? 'checked' : '')) : ''
        );
    }
}

?>