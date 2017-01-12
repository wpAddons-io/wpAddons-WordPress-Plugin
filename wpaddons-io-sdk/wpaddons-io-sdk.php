<?php
/**
 * Addons
 *
 * @version 1.0
 * @author  wpAddons.io
 * @license https://opensource.org/licenses/gpl-license GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'wpAddons' ) ) {
	/**
	 * Addons Class
	 *
	 * A general class for addons page.
	 *
	 * @since 1.0
	 */
	class wpAddons {

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
		 * Admin Page Slug
		 *
		 * @since 1.0
		 *
		 * @access public
		 *
		 * @var string Unique slug for the admin page.
		 */
		public $admin_page_slug = '';

		/**
		 * Parant Admin Page Slug
		 *
		 * @since 1.0
		 *
		 * @access public
		 *
		 * @var string The slug of the parent admin page.
		 */
		public $parant_admin_page_slug = '';

		/**
		 * Admin Page Title
		 *
		 * @since 1.0
		 *
		 * @access public
		 *
		 * @var string The text to be displayed in the title of the admin page.
		 */
		public $admin_page_title = '';

		/**
		 * Menu Title
		 *
		 * @since 1.0
		 *
		 * @access public
		 *
		 * @var string The text to be displayed in the dashboard sidebar menu.
		 */
		public $menu_title = '';

		/**
		 * Menu Title Color
		 *
		 * @since 1.0
		 *
		 * @access public
		 *
		 * @var string The color of the text displayed in the dashboard sidebar menu.
		 */
		public $menu_title_color = '';

		/**
		 * Capability
		 *
		 * @since 1.0
		 *
		 * @access public
		 *
		 * @var string The capability required for this menu to be displayed to the user.
		 */
		public $capability = '';

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

			// Set class properties
			$this->set_properties( $args );

			// Register and enqueues styles
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );

			// Register admin menu page
			add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ), 999 );

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
				'debug_mode'             => false,
				'parant_plugin_slug'     => '',
				'parant_admin_page_slug' => '',
				'admin_page_slug'        => '',
				'admin_page_title'       => '',
				'menu_title'             => '',
				'menu_title_color'       => '',
				'capability'             => 'manage_options',
				'view'                   => plugin_dir_path( __FILE__ ) . 'view/wordpress-plugins.php',
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
		 * Register and Enqueues front-end styles
		 *
		 * @since 1.0
		 *
		 * @access public
		 *
		 * @return void
		 */
		public function enqueue_styles() {

			wp_register_style( "{$this->parant_plugin_slug}_sitestyle", plugins_url( 'css/wpaddons-io.css', __FILE__ ) );

			wp_enqueue_style( "{$this->parant_plugin_slug}_sitestyle" );

		}

		/**
		 * Add Admin Menu Page
		 *
		 * Register dashboard submenu page for the addons/extentions.
		 *
		 * @since 1.0
		 *
		 * @access public
		 *
		 * @return false|string The resulting page's hook_suffix, or false if the user does not have the required capability.
		 */
		public function add_admin_menu_page() {

			// Check whether the dashboard sidebar menu has a custom color
			if ( $this->menu_title_color ) {
				$menu_title = sprintf( '<span style="color: %1$s;">%2$s</span>', $this->menu_title_color, $this->menu_title );
			} else {
				$menu_title = $this->menu_title;
			}

			// add submenu to the plugin
			return add_submenu_page(
				$this->parant_admin_page_slug,
				$this->admin_page_title,
				$menu_title,
				$this->capability,
				$this->admin_page_slug,
				array( $this, 'page_layout' )
			);

		}

		/**
		 * Page Layout
		 *
		 * Render the page content.
		 *
		 * @since 1.0
		 *
		 * @access public
		 *
		 * @return void
		 */
		public function page_layout() {
			$wrap_class = str_replace( '_', '-', $this->parant_plugin_slug );
			?>
			<div id="wpaddons-io-wrap" class="wrap <?php echo $wrap_class; ?>-wrap">

				<h1><?php echo get_admin_page_title(); ?></h1>

				<?php
				/**
				 * Fires bellow the page title.
				 *
				 * @since 1.0
				 *
				 * @param object $this The addons class.
				 */
				do_action( "{$this->parant_plugin_slug}_below_title", $this );

				// Display the items from remote server.
				$this->display_items();

				/**
				 * Fires bellow the items.
				 *
				 * @since 1.0
				 *
				 * @param object $this The addons class.
				 */
				do_action( "{$this->parant_plugin_slug}_below_items", $this );
				?>

			</div>
			<?php
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
		 * @return string HTML formatted list of all the items
		 */
		public function display_items() {

			// Get addon
			$addons = $this->get_addons();

			// Load the display template
			include_once( $this->view );

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
				delete_transient( "{$this->parant_plugin_slug}_addons" );
			}

			// Get cached data currently stored on the sites database
			$data = get_transient( "{$this->parant_plugin_slug}_addons" );

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
			set_transient( "{$this->parant_plugin_slug}_addons", $data, 6 * HOUR_IN_SECONDS );

			// Return data
			return $data;
		}

	}

}
