<?php
/*
  Plugin Name: Widz
  Plugin URI: http://wordpress.org/plugins/widz/
  Description: Widz is a WordPress Widget that helps user easy to create Tab Item by available widget and display as Tabbed on the Front end in Sidebar
  Author: vutuansw
  Version: 1.0.0
  Author URI: https://vutuansw.wordpress.com/
  License: GPLv3
  License URI: URI: https://www.gnu.org/licenses/gpl-3.0.html
  Requires at least: 4.5
  Tested up to: 4.7
 */

class Widz {

	/**
	 * Widz version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The single instance of the class.
	 *
	 * @var Widz
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Widz Instance.
	 *
	 * Ensures only one instance of Widz is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see widz()
	 * @return Widz - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Init plugin
	 * @since 1.0
	 */
	public function __construct() {
		$this->defined();
		$this->includes();
		$this->hooks();
	}

	/**
	 * Main hook in plugin
	 * @since 1.0
	 */
	public function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'admin_init', array( $this, 'admin_fields' ) );
	}

	/**
	 * Load field in admin
	 * @since 1.0
	 */
	public function admin_fields() {
		include WIDZ_DIR . 'includes/admin-fields/field_default.php';
		include WIDZ_DIR . 'includes/admin-fields/field_icon_picker.php';
		include WIDZ_DIR . 'includes/admin-fields/field_widget.php';
	}

	/**
	 * Include library
	 * @since 1.0
	 */
	public function includes() {
		include WIDZ_DIR . 'includes/class-wz-widget.php';
		include WIDZ_DIR . 'includes/widgets/tabbed.php';
	}

	/**
	 * Defined
	 * @since 1.0
	 */
	public function defined() {
		define( 'WIDZ_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		define( 'WIDZ_VERSION', $this->version );
		define( 'WIDZ_DIR', plugin_dir_path( __FILE__ ) );
		define( 'WIDZ_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Load Localisation files.
	 * @since 1.0
	 * @return void
	 */
	public function load_plugin_textdomain() {

		// Set filter for plugin's languages directory
		$widz_dir = WIDZ_DIR . 'languages/';
		$widz_dir = apply_filters( 'widz_languages_directory', $widz_dir );

		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale', get_locale(), 'widz' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'widz', $locale );

		// Setup paths to current locale file
		$mofile_local = $widz_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/widz/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/epl folder
			load_textdomain( 'widz', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/widz/languages/ folder
			load_textdomain( 'widz', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'widz', false, $widz_dir );
		}
	}

	/**
	 * Enqueue admin scripts
	 * @since 1.0
	 * @return void
	 */
	public function admin_scripts( $hook_suffix ) {

		$min = WP_DEBUG ? '' : '.min';

		global $widz_registered_fields;

		if ( !empty( $widz_registered_fields ) ) {

			$widz_registered_fields = array_unique( $widz_registered_fields );

			wp_enqueue_style( 'font-awesome', WIDZ_URL . 'assets/css/font-awesome' . $min . '.css', null, '4.7.0' );
			wp_enqueue_style( 'widz-admin', WIDZ_URL . 'assets/css/admin' . $min . '.css', null, WIDZ_VERSION );

			wp_enqueue_script( 'widz-libs', WIDZ_URL . 'assets/js/libs' . $min . '.js', array( 'jquery' ), WIDZ_VERSION );
			wp_enqueue_script( 'widz-admin', WIDZ_URL . 'assets/js/admin_fields' . $min . '.js', array( 'jquery' ), WIDZ_VERSION );

			$upload_dir = wp_upload_dir();

			wp_localize_script( 'widz-admin', 'widz_var', array(
				'upload_url' => $upload_dir['baseurl']
			) );

			foreach ( $widz_registered_fields as $type ) {
				switch ( $type ) {
					case 'color_picker':
						wp_enqueue_script( 'wp-color-picker' );
						wp_enqueue_style( 'wp-color-picker' );
						break;
					case 'icon_picker':
					case 'widget':
						wp_enqueue_script( 'font-iconpicker', WIDZ_URL . 'assets/vendors/fonticonpicker/js/jquery.fonticonpicker' . $min . '.js', array( 'jquery' ), WIDZ_VERSION );
						wp_enqueue_style( 'font-iconpicker', WIDZ_URL . 'assets/vendors/fonticonpicker/css/jquery.fonticonpicker' . $min . '.css', null, WIDZ_VERSION );
						break;
					default :

						break;
				}
			}
		}
	}

	/**
	 * Enqueue front-end scripts
	 * @since 1.0
	 * @return void
	 */
	public function front_scripts() {
		$min = WP_DEBUG ? '' : '.min';

		wp_enqueue_style( 'font-awesome', WIDZ_URL . 'assets/css/font-awesome' . $min . '.css', null, '4.7.0' );
		wp_enqueue_style( 'widz-front', WIDZ_URL . 'assets/css/front' . $min . '.css', null, WIDZ_VERSION );
		wp_enqueue_script( 'widz-front', WIDZ_URL . 'assets/js/front' . $min . '.js', array( 'jquery' ), WIDZ_VERSION );
	}

}

/**
 * Main instance of Widz.
 *
 * Returns the main instance of Widz to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return Widz
 */
function widz() {
	return Widz::instance();
}

// Global for backwards compatibility.
$GLOBALS['widz'] = widz();
