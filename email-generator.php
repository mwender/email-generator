<?php
/**
 * Plugin Name:     Email Generator
 * Plugin URI:      https://github.com/mwender/email-generator
 * Description:     Generate your own HTML emails using a combination of templates and variables. Then copy-and-paste the generated code into your favorite email service.
 * Author:          TheWebist
 * Author URI:      https://mwender.com
 * Text Domain:     email-generator
 * Domain Path:     /languages
 * Version:         1.2.0
 *
 * @package         Email_Generator
 */

if ( ! defined( 'ABSPATH' ) )
  return;

require_once( plugin_dir_path(__FILE__ ) . 'vendor/autoload.php' );
require_once( plugin_dir_path( __FILE__ ) . 'lib/fns/email-template-cpt.php' );
require_once( plugin_dir_path( __FILE__ ) . 'lib/fns/email-template-cpt-options.php' );

