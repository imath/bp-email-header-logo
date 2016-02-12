<?php
/**
 * A logo for your BuddyPress emails
 *
 * @package   BP Email Header Logo
 * @author    imath
 * @license   GPL-2.0+
 * @link      https://imathi.eu
 *
 * @buddypress-plugin
 * Plugin Name:       BP Email Header Logo
 * Plugin URI:        https://github.com/imath/bp-email-header-logo
 * Description:       Use the WordPress Site Icon feature to add your site logo to the BuddyPress emails header
 * Version:           1.0.0-alpha
 * Author:            imath
 * Author URI:        https://github.com/imath
 * Text Domain:       bp-email-header-logo
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages/
 * GitHub Plugin URI: https://github.com/imath/bp-email-header-logo
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BP_Email_Logo' ) ) :
/**
 * Main Class
 *
 * @since 1.0.0
 */
class BP_Email_Logo {
	/**
	 * Instance of this class.
	 */
	protected static $instance = null;

	/**
	 * BuddyPress version
	 */
	public static $required_bp_version = '2.5.0-beta1';

	/**
	 * Initialize the plugin
	 */
	private function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_hooks();
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 */
	public static function start() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Sets some globals for the plugin
	 *
	 * @since 1.0.0
	 */
	private function setup_globals() {
		/** Plugin globals ********************************************/
		$this->version       = '1.0.0-alpha';
		$this->domain        = 'bp-email-header-logo';
		$this->name          = 'BP Email Header Logo';
		$this->file          = __FILE__;
		$this->basename      = plugin_basename( $this->file );
		$this->plugin_dir    = plugin_dir_path( $this->file );
		$this->plugin_url    = plugin_dir_url( $this->file );
		$this->includes_dir  = trailingslashit( $this->plugin_dir . 'includes'  );
		$this->templates_dir = $this->plugin_dir . 'template';
		$this->lang_dir      = trailingslashit( $this->plugin_dir . 'languages' );
		$this->has_site_icon = has_site_icon( bp_get_root_blog_id() );
	}

	/**
	 * Checks BuddyPress version
	 *
	 * @since 1.0.0
	 */
	public function version_check() {
		// taking no risk
		if ( ! function_exists( 'bp_get_version' ) ) {
			return false;
		}

		return version_compare( bp_get_version(), self::$required_bp_version, '>=' );
	}

	/**
	 * Include needed files
	 *
	 * @since 1.0.0
	 */
	private function includes() {
		if ( ! $this->version_check() || ! $this->has_site_icon ) {
			return;
		}

		require( $this->includes_dir . 'template-tags.php' );
	}

	/**
	 * Set hooks
	 *
	 * @since 1.0.0
	 */
	private function setup_hooks() {
		// BuddyPress version is ok & the site has an icon
		if ( $this->version_check() && $this->has_site_icon ) {
			// Include the site_icon setting
			add_filter( 'bp_email_get_customizer_settings', array( $this, 'email_settings' ), 10, 1 );

			// Add the customizer control
			add_action( 'bp_email_customizer_register_sections', array( $this, 'email_control' ), 10, 1 );

			// Add the email template to the stack
			add_action( 'bp_register_theme_directory', array( $this, 'register_template_dir' ) );

			// Include the Site icon
			add_filter( 'bp_email_hl_get_site_name', array( $this, 'print_header_logo' ), 10, 2 );

		// There's something wrong, inform the Administrator
		} else {
			add_action( bp_core_do_network_admin() ? 'network_admin_notices' : 'admin_notices', array( $this, 'admin_warning' ) );
		}

		// load the languages..
		add_action( 'bp_init', array( $this, 'load_textdomain' ), 5 );
	}

	/**
	 * Add the email setting to bp_email_options
	 *
	 * @since 1.0.0
	 */
	public function email_settings( $settings = array() ) {
		return array_merge( $settings, array(
			'bp_email_options[site_icon]' => array(
				'capability'        => 'bp_moderate',
				'default'           => false,
				'sanitize_callback' => array( $this, 'sanitize_setting' ),
				'transport'         => 'refresh',
				'type'              => 'option',
			),
		) );
	}

	/**
	 * Make sure the option is a boolean
	 *
	 * @since 1.0.0
	 */
	public function sanitize_setting( $bp_email_option = false ) {
		return (bool) $bp_email_option;
	}

	/**
	 * Add a checkbox control to the Email Header section
	 *
	 * @since 1.0.0
	 */
	public function email_control( WP_Customize_Manager $wp_customizer ) {
		$wp_customizer->add_control( 'bp_email_header_logo', array(
			'settings' => 'bp_email_options[site_icon]',
			'label'    => __( 'Add the site logo', 'bp-email-header-logo' ),
			'section'  => 'section_bp_mailtpl_header',
			'type'     => 'checkbox',
			'priority' => 100
		) );
	}

	/**
	 * Register the template dir into BuddyPress template stack
	 *
	 * @since 1.0.0
	 */
	public function register_template_dir() {
		bp_register_template_stack( array( $this, 'template_dir' ), 13 );
	}

	/**
	 * Are we using the email template ?
	 *
	 * @since 1.0.0
	 */
	public function is_email_template() {
		return did_action( 'bp_email' ) || ( get_post_type() === bp_get_email_post_type() && is_single() );
	}

	/**
	 * Set the template dir
	 *
	 * @since 1.0.0
	 */
	public function template_dir() {
		if ( ! $this->is_email_template() ) {
			return;
		}

		return apply_filters( 'bp_email_header_logo_template_dir', $this->templates_dir );
	}

	/**
	 * Append the Site Icon to The Site name inside the Email Template
	 *
	 * @since 1.0.0
	 */
	public function print_header_logo( $site_name, $settings = array() ) {
		$output = '';

		if ( ! empty( $settings['site_icon'] ) ) {
			$output = sprintf( '<img src="%s" style="vertical-align: top"/>&nbsp', get_site_icon_url( 32, '', bp_get_root_blog_id() ) );
		}

		return $output . $site_name;
	}

	/**
	 * Display a message to admin in case config is not as expected
	 *
	 * @since 1.0.0
	 */
	public function admin_warning() {
		$warnings = array();

		if( ! $this->version_check() ) {
			$warnings[] = sprintf( __( '%s requires at least version %s of BuddyPress.', 'bp-email-header-logo' ), $this->name, '2.5.0' );
		}

		if ( ! $this->has_site_icon ) {
			$warnings[] = sprintf( __( '%s requires the site icon to be set.', 'bp-email-header-logo' ), $this->name );
		}

		if ( ! empty( $warnings ) ) :
		?>
		<div id="message" class="error">
			<?php foreach ( $warnings as $warning ) : ?>
				<p><?php echo esc_html( $warning ) ; ?>
			<?php endforeach ; ?>
		</div>
		<?php
		endif;
	}

	/**
	 * Loads the translation files
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale', get_locale(), $this->domain );
		$mofile        = sprintf( '%1$s-%2$s.mo', $this->domain, $locale );

		// Setup paths to current locale file
		$mofile_local  = $this->lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/bp-email-header-logo/' . $mofile;

		// Look in global /wp-content/languages/bp-email-header-logo folder
		load_textdomain( $this->domain, $mofile_global );

		// Look in local /wp-content/plugins/bp-email-header-logo/languages/ folder
		load_textdomain( $this->domain, $mofile_local );
	}
}

endif;

// Let's start !
function bp_email_header_logo() {
	return BP_Email_Logo::start();
}
add_action( 'bp_include', 'bp_email_header_logo', 9 );
