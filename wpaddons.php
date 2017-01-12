<?php
/**
 * Plugin Name: wpAddons
 * Plugin URI:  https://wpAddons.io/
 * Description: Display addons and extensions from wpAddons.io using a simple shortcode.
 * Version:     1.0
 * Author:      Rami Yushuvaev
 * Author URI:  https://GenerateWP.com/
 * Text Domain: wpaddons
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP Addons IO Class
 *
 * @since 1.0
 */
class WP_Addons_IO {

	/**
	 * Debug Mode
	 *
	 * @since 1.0
	 *
	 * @access public
	 *
	 * @var bool Whether to activate the debug mode, or not. Default is false.
	 */
	public $debug_mode = false;

	/**
	 * Parant Plugin Slug
	 *
	 * @since 1.0
	 *
	 * @access public
	 *
	 * @var string The slug of the parant plugin in WordPress plugin repository.
	 */
	public $parant_plugin_slug = '';

	/**
	 * View
	 *
	 * @since 1.0
	 *
	 * @access public
	 *
	 * @var string The template displayed to the user.
	 */
	public $view = '';

	/**
	 * Class Constructor
	 *
	 * Get things started.
	 *
	 * @since 1.0
	 *
	 * @access public
	 *
	 * @param array $args 
	 */
	public function __construct( $args = array() ) {

		// Register and enqueues styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		// Set class properties
		$this->set_properties( $args );

		// Render items
		$this->display_items();

	}

	/**
	 * Register and Enqueues styles
	 *
	 * @since 1.0
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function enqueue_styles() {

		wp_register_style( 'wpaddons_io_sitestyle', plugins_url( 'wpaddons-io-sdk/css/wpaddons-io.css', __FILE__ ) );

		wp_enqueue_style( 'wpaddons_io_sitestyle' );

	}

	/**
	 * Set Properties
	 *
	 * Define class properties, based on the defined properties and the default values.
	 *
	 * @since 1.0
	 *
	 * @access public
	 *
	 * @param array $args 
	 *
	 * @return void
	 */
	public function set_properties( $args ) {

		// Reset default property values
		$reset = array(
			'debug_mode'         => false,
			'parant_plugin_slug' => '',
			'view'               => plugin_dir_path( __FILE__ ) . 'wpaddons-io-sdk/view/wordpress-plugins.php',
		);

		// Define properties
		foreach ( $reset as $name => $default ) {

			if ( array_key_exists( $name, $args ) ) {
				// If set, use defined values
				$this->{$name} = $args[$name];
			} else {
				// If not set, use default values
				$this->{$name} = $default;
			}

		}

	}

	/**
	 * Render Items
	 *
	 * Display the items from remote server.
	 *
	 * @since 1.0
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function display_items() {
		$wrap_class = str_replace( '_', '-', $this->parant_plugin_slug );
		?>
		<div id="wpaddons-io-wrap" class="wrap <?php echo $wrap_class; ?>-wrap">

			<?php
			// Get addon
			$addons = $this->get_addons();

			// Load the display template
			include_once( $this->view );
			?>

		</div>
		<?php
	}

	/**
	 * Get Addons
	 *
	 * Retrieve the addons from remote server, or load stored cached data from the database.
	 * You can force fetching fresh data from remote server be setting `debug_mode` to TRUE,
	 * when initiating the class.
	 *
	 * @since 1.0
	 *
	 * @access public
	 *
	 * @return string A list of addons.
	 */
	public function get_addons() {

		/**
		 * Check whether debug mode is enabled to force fetching fresh data from remote server,
		 * by deleting the data currently stored on the sites database.
		 */
		if ( $this->debug_mode ) {
			delete_transient( "wpaddonsio_{$this->parant_plugin_slug}" );
		}

		// Get cached data currently stored on the sites database
		$data = get_transient( "wpaddonsio_{$this->parant_plugin_slug}" );

		// Return chached data, if transient exists and it's not expired yet
		if ( false !== $data ) {
			return $data;
		}

		// Get fresh data from remote server
		$response = wp_remote_get(
			sprintf( 'https://wpaddons.io/wp-json/plugin/%s/', $this->parant_plugin_slug ),
			array( 'sslverify' => false )
		);

		// Return empty json on error or request status is other than 200 (request succeeded)
		if ( is_wp_error( $response ) || ( 200 != wp_remote_retrieve_response_code( $response ) ) ) {
			return json_encode ( json_decode ("{}") );
		}

		// Decode json
		$data = json_decode( wp_remote_retrieve_body( $response ) );

		// Caching: Save data on sites database using transient
		set_transient( "wpaddonsio_{$this->parant_plugin_slug}", $data, 6 * HOUR_IN_SECONDS );

		// Return data
		return $data;
	}

}

/**
 * Addons Shortcode
 *
 * Shortcode to display addons assigned to a specific plugin.
 *
 * @since 1.0
 */
function wpaddons_shortcode( $atts ) {

	// Register and enqueues styles
	add_action( 'wp_enqueue_scripts', array( 'WP_Addons_IO', 'enqueue_styles' ) );

	$atts = shortcode_atts(
		array(
			'debug_mode' => 0,
			'plugin'     => '',
			'view'       => 'cover-grid-third',
		),
		$atts,
		'wpaddons'
	);

	// Load wpAddons SDK
	//require_once plugin_dir_path( __FILE__ ) . '/wpaddons-io-sdk/wpaddons-io-sdk.php';

	// Set addon parameters
	$plugin_data = array(
		'parant_plugin_slug' => $atts['plugin'],
		'view'               => plugin_dir_path( __FILE__ ) . 'wpaddons-io-sdk/view/' . $atts['view'] . '.php',
	);

	// Initiate addons
	new WP_Addons_IO( $plugin_data );

}
add_shortcode( 'wpaddons', 'wpaddons_shortcode' );
