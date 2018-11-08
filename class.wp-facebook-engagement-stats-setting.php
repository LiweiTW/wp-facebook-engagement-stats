<?php
class WPFacebookEngagementStats {

	static $instance = false;
	const fb_api_url = "https://graph.facebook.com/v3.2/";

//    Constructor.  Initializes WordPress hooks
    private function __construct() {
        add_action( 'admin_init', array( __CLASS__, 'wp_facebook_engagement_stats_setting_initialization' ) );
        add_filter( 'manage_posts_columns' , array( __CLASS__, 'wp_facebook_engagement_stats_columns_reg' ) );
        add_action( 'manage_posts_custom_column',  array( __CLASS__, 'wp_facebook_engagement_stats_columns_show') );
    }

    public static function init() {
        if ( ! self::$instance ) {
            self::$instance = new WPFacebookEngagementStats;
        }
        return self::$instance;
    }

    private static function get_access_token() {
        return get_option( 'wp-facebook-engagement-stats-access-token', '' );
    }

    function wp_facebook_engagement_stats_setting_initialization (){
    	add_settings_section(
			'wp-facebook-engagement-stats-setting',
			'WP Facebook Engagement Stats Setting',
			'wp_facebook_engagement_stats_setting_label',
			'discussion'
		);

		add_settings_field(
			'wp-facebook-engagement-stats-access-token',
			'Access Token',
			array( __CLASS__, 'wp_facebook_engagement_stats_setting_input' ),
			'discussion',
			'wp-facebook-engagement-stats-setting'
		);

		register_setting( 'discussion', 'wp-facebook-engagement-stats-access-token' );

    }



	function wp_facebook_engagement_stats_setting_label() {
		echo '<p>請輸入 Access Token</p>';
	}

	function wp_facebook_engagement_stats_setting_input() {
		$access_token = self::get_access_token();
		echo '<input name="wp-facebook-engagement-stats-access-token" id="wp-facebook-engagement-stats-access-token" type="password" value="' . $access_token . '">';
	}

	function wp_facebook_engagement_stats_columns_reg( $columns ) {
		return array_merge( $columns, 
              array('fb-engagement' => __('Facebook Engagement')) );
	}

	function wp_facebook_engagement_stats_columns_show( $name ) {
	    switch ($name) {
	        case 'fb-engagement':
	        	if (self::get_access_token() === "") return;
		        $params = http_build_query( array(
					'id'           => esc_url( get_permalink( get_the_ID() ) ),
					'fields'       => "engagement",
					'access_token' => self::get_access_token(),
					'method'       => "GET"
				) );

				$response = wp_remote_post( self::fb_api_url . '?' . $params );
				if ( is_wp_error( $response ) ) {
					echo "No Result";
					return false;
				} else {
					$body = wp_remote_retrieve_body( $response );
                    $json = json_decode( $body );
                    if ( $json ) {
                            $results = $json->engagement;
                            echo $results->reaction_count . " / " . $results->comment_count . " / " . $results->share_count;
                            return true;
                    } else {
                            echo "No Result";
                            return false;
                    }
				}
				
    	}
	}
}

?>
