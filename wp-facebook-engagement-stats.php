<?php
/*
Plugin Name:  WP Facebook Engagement Stats
Plugin URI:
Description:  Monitor posts engagement on Facebook.
Version:      0.1.1
Author:       LiweiTW
Author URI:
License:      GPLv2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

require_once dirname( __FILE__ ) . '/class.wp-facebook-engagement-stats-setting.php';
WPFacebookEngagementStats::init();