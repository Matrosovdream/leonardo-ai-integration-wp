<?php
/**
 * Leonardo.ai Integration

 * @wordpress-plugin
 * Plugin Name:       Leonardo.ai Integration
 * Plugin URI:        
 * Description:       Leonardo.ai Integration
 * Version:           1.0
 * Author:            Stanislav Matrosov
 */

if ( ! defined( 'LEONARDO_AI_ABSPATH' ) ) {
	define( 'LEONARDO_AI_ABSPATH', plugin_dir_path( __FILE__ ) );
}

require_once('classes/generation.class.php');
require_once('classes/http.class.php');
require_once('classes/api.class.php');
require_once('classes/settings.class.php');
require_once('classes/cron.class.php');
require_once('classes/post-helper.class.php');
require_once('hooks/post.hooks.php');
require_once('ajax/admin.ajax.php');