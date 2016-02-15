<?php
/**
* Plugin Name: UCF Header Bar Plugin
* Plugin URI: https://universityheader.ucf.edu/
* Description: Enqueue the UCF Header Bar into any WordPress website. Options page for sites with a max-width larger than 1200px.
* Version: 1.0 
* Author: Jonathan Hendrcker
* Author URI: http://www.cos.ucf.edu/it
* License: GPL12
*/

defined( 'ABSPATH' ) or die( '' );

/**
 * Plugin Option Page  - Generated at http://jeremyhixon.com/wp-tools/option-page/
 */
class UCFHeaderBar {
	private $ucf_header_bar_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'ucf_header_bar_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'ucf_header_bar_page_init' ) );
	}

	public function ucf_header_bar_add_plugin_page() {
		add_options_page(
			'UCF Header Bar', // page_title
			'UCF Header Bar', // menu_title
			'manage_options', // capability
			'ucf-header-bar', // menu_slug
			array( $this, 'ucf_header_bar_create_admin_page' ) // function
		);
	}

	public function ucf_header_bar_create_admin_page() {
		$this->ucf_header_bar_options = get_option( 'ucf_header_bar_option_name' ); ?>

		<div class="wrap">
			<h2>UCF Header Bar</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'ucf_header_bar_option_group' );
					do_settings_sections( 'ucf-header-bar-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function ucf_header_bar_page_init() {
		register_setting(
			'ucf_header_bar_option_group', // option_group
			'ucf_header_bar_option_name', // option_name
			array( $this, 'ucf_header_bar_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'ucf_header_bar_setting_section', // id
			'UCF Header Bar Settings', // title
			array( $this, 'ucf_header_bar_section_info' ), // callback
			'ucf-header-bar-admin' // page
		);

		add_settings_field(
			'site_s_max_width_is_larger_than_1200px_0', // id
			'Site\'s max-width is larger than 1200px.', // title
			array( $this, 'site_s_max_width_is_larger_than_1200px_0_callback' ), // callback
			'ucf-header-bar-admin', // page
			'ucf_header_bar_setting_section' // section
		);
	}

	public function ucf_header_bar_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['site_s_max_width_is_larger_than_1200px_0'] ) ) {
			$sanitary_values['site_s_max_width_is_larger_than_1200px_0'] = $input['site_s_max_width_is_larger_than_1200px_0'];
		}

		return $sanitary_values;
	}

	public function ucf_header_bar_section_info() {
		
	}

	public function site_s_max_width_is_larger_than_1200px_0_callback() {
		printf(
			'<input type="checkbox" name="ucf_header_bar_option_name[site_s_max_width_is_larger_than_1200px_0]" id="site_s_max_width_is_larger_than_1200px_0" value="site_s_max_width_is_larger_than_1200px_0" %s>',
			( isset( $this->ucf_header_bar_options['site_s_max_width_is_larger_than_1200px_0'] ) && $this->ucf_header_bar_options['site_s_max_width_is_larger_than_1200px_0'] === 'site_s_max_width_is_larger_than_1200px_0' ) ? 'checked' : ''
		);
	}
}
if ( is_admin() )
	$ucf_header_bar = new UCFHeaderBar();

/** 
 * Retrieve this value with:
 * $ucf_header_bar_options = get_option( 'ucf_header_bar_option_name' ); // Array of All Options
 **/

/**
 * Enqueue the UCF Header Script
 **/
function ucf_header_script() {

	$handle 	= 'ucfhb-script';
	$list 		= 'enqueued';
	$wide_site	= '';

	// Check for options page value
	$ucf_header_bar_options = get_option( 'ucf_header_bar_option_name' );
	if ( !empty($ucf_header_bar_options ) )
		$wide_site 	=  $ucf_header_bar_options['site_s_max_width_is_larger_than_1200px_0'];	

	// Check for duplicate script
	if( wp_script_is( $handle, $list ) )		
		return;
	else {
		// Register the >1200px script version
		if ( $wide_site === 'site_s_max_width_is_larger_than_1200px_0' )
			wp_register_script( 'ucfhb-script', '//universityheader.ucf.edu/bar/js/university-header.js?use-1200-breakpoint=1');
		// Regsister the default version
		else 
			wp_register_script( 'ucfhb-script', '//universityheader.ucf.edu/bar/js/university-header.js');
		wp_enqueue_script( 'ucfhb-script' );
	}
} 
add_action ('wp_enqueue_scripts', 'ucf_header_script');

/**
 * Add ID attribute to registered University Header script.
 **/
function add_id_to_ucfhb($url) {
    if ( (false !== strpos($url, 'bar/js/university-header.js')) || (false !== strpos($url, 'bar/js/university-header-full.js')) ) {
      remove_filter('clean_url', 'add_id_to_ucfhb', 10, 3);
      return "$url' id='ucfhb-script";
    }
    return $url;
}
add_filter('clean_url', 'add_id_to_ucfhb', 10, 3);
