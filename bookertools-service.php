<?php

 class codefairies_bookertools_service
 {
     /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $baseUrl ;

    /**
     * Start up
     */
    public function __construct()
    {
        // Set class property
        $this->options = get_option( 'bookertools-hash' );
        $this->baseUrl = 'api/public/v2';
    }

    private function codefairies_bookertools_Get($url){
        $result = null;
        if(isset($this->options['team_url']) &&
            isset($this->options['team_token']) && isset($this->options['team_has_token'])
            && $this->options['team_has_token'] == '1')
        {
            $args = array(
                'timeout'     => 5,
                'headers' => array(
                    'Authorization' => 'bearer ' . $this->options['team_token']
                )
            );
            $url = $this->options['team_url'] . '/' . $this->baseUrl . '/' . $url;
			//codefairies_bookertools_debug_to_console($url);
            $response = wp_remote_get( $url, $args );
            if(is_wp_error($response)){
                // wordpress was not able to process this request
                //log_to_bookertools($response->get_error_message());
                $result = null;
            }else{
                $http_code = wp_remote_retrieve_response_code( $response );
                if($http_code == 200){
                     $body = json_decode(wp_remote_retrieve_body( $response ));
                     $result = $body;
                }else{
                    //log_to_bookertools('no http 200 for url ' . $url);
                    $result = null;
                }
            }
        }
        return $result;
    }

    public function codefairies_bookertools_GetFutureShow($subtype,$subtypenaam,$limit){
        $result;
        if(!is_int($limit)) {
            $limit = 1000;
        }
        $url = 'shows?limit=' .$limit;
		if(strlen($subtype)>0){
			$url.='&'.$subtypenaam.'='.$subtype;
		}
        $result = $this->codefairies_bookertools_Get($url);
        if($result == null) {
            $result = json_decode('{"isAuthorized":true,"data":[],"errors":null}', true);
        }
        return $result;
    }

	public function codefairies_bookertools_GetFutureTours($subtype,$subtypenaam,$limit){
        $result;
        if(!is_int($limit)) {
            $limit = 1000;
        }
        $url = 'tours?limit=' .$limit;
		if(strlen($subtype)>0){
			$url.='&'.$subtypenaam.'='.$subtype;
		}
        $result = $this->codefairies_bookertools_Get($url);
        if($result == null) {
            $result = json_decode('{"isAuthorized":true,"data":[],"errors":null}', true);
        }
        return $result;
    }
 }
 ?>