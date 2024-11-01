<?php
/**
 * Plugin Name:			Storefront Wow Sharing Lite
 * Plugin URI:			http://wordpress.org/plugins/storefront-wow-sharing-lite
 * Description:			Add social share buttons to single product page
 * Version:				1.0.5
 * Author:				Disenialia
 * Author URI:			https://disenialia.com/
 * Requires at least:	4.7.0
 * Tested up to:		5.3.2
 * Requires PHP:        5.6
 *
 * Text Domain: storefront-wow-sharing-lite
 * Domain Path: /languages/
 *
 * @package Storefront_Wow_Sharing_Lite
 * @category Core
 * @author Disenialia
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Returns the main instance of Storefront_Wow_Sharing_Lite to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Storefront_Wow_Sharing_Lite
 */
function Storefront_Wow_Sharing_Lite() {
	return Storefront_Wow_Sharing_Lite::instance();
} // End Storefront_Wow_Sharing_Lite()

Storefront_Wow_Sharing_Lite();

/**
 * Main Storefront_Wow_Sharing_Lite Class
 *
 * @class Storefront_Wow_Sharing_Lite
 * @version	1.0.0
 * @since 1.0.0
 * @package	Storefront_Wow_Sharing_Lite
 */
final class Storefront_Wow_Sharing_Lite {
	/**
	 * Storefront_Wow_Sharing_Lite The single instance of Storefront_Wow_Sharing_Lite.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct() {
		$this->token 			= 'storefront-wow-sharing-lite';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.0';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'swowsharinglite_load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'swowsharinglite_setup' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'swowsharinglite_plugin_links' ) );
	}

	/**
	 * Main Storefront_Wow_Sharing_Lite Instance
	 *
	 * Ensures only one instance of Storefront_Wow_Sharing_Lite is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Storefront_Wow_Sharing_Lite()
	 * @return Main Storefront_Wow_Sharing_Lite instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function swowsharinglite_load_plugin_textdomain() {
		load_plugin_textdomain( 'storefront-wow-sharing-lite', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Plugin page links
	 *
	 * @since  1.0.0
	 */
	public function swowsharinglite_plugin_links( $links ) {
		$plugin_links = array(
			'<a target="_blank" href="https://disenialia.com/store/support/">' . __( 'Support', 'storefront-wow-sharing-lite' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();

		// get theme customizer url
		$url = admin_url() . 'customize.php?';
		$url .= 'url=' . urlencode( site_url() . '?storefront-customizer=true' ) ;
		$url .= '&return=' . urlencode( admin_url() . 'plugins.php' );
		$url .= '&storefront-customizer=true';

		$notices 		= get_option( 'swowsharinglite_activation_notice', array() );
		$notices[]		= sprintf( __( '%sThanks for installing the Storefront Wow Sharing extension. To get started, visit the %sCustomizer%s.%s %sOpen the Customizer%s', 'storefront-wow-sharing-lite' ), '<p>', '<a href="' . esc_url( $url ) . '">', '</a>', '</p>', '<p><a href="' . esc_url( $url ) . '" class="button button-primary">', '</a></p>' );

		update_option( 'swowsharinglite_activation_notice', $notices );
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Setup all the things.
	 * Only executes if Storefront or a child theme using Storefront as a parent is active and the extension specific filter returns true.
	 * Child themes can disable this extension using the storefront_extension_boilerplate_enabled filter
	 * @return void
	 */
	public function swowsharinglite_setup() {

		if ( 'storefront' == get_option( 'template' ) && apply_filters( 'storefront_wow_sharing_lite_supported', true ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'swowsharinglite_styles' ), 999 );
			add_action( 'customize_register', array( $this, 'swowsharinglite_customize_register' ) );
			add_action( 'customize_preview_init', array( $this, 'swowsharinglite_customize_preview_js' ) );
			add_filter( 'body_class', array( $this, 'swowsharinglite_body_class' ) );

			add_action( 'woocommerce_share', array( $this, 'swowsharinglite_layout_adjustments' ), 5 );

			add_action( 'admin_notices', array( $this, 'swowsharinglite_customizer_notice' ) );

			// Hide the 'More' section in the customizer
			add_filter( 'storefront_customizer_more', '__return_false' );
		} else {
			add_action( 'admin_notices', array( $this, 'swowsharinglite_install_storefront_notice' ) );
		}
	}

	/**
	 * Admin notice
	 * Checks the notice setup in install(). If it exists display it then delete the option so it's not displayed again.
	 * @since   1.0.0
	 * @return  void
	 */
	public function swowsharinglite_customizer_notice() {
		$notices = get_option( 'swowsharinglite_activation_notice' );

		if ( $notices = get_option( 'swowsharinglite_activation_notice' ) ) {

			foreach ( $notices as $notice ) {
				echo '<div class="notice is-dismissible updated">' . $notice . '</div>';
			}

			delete_option( 'swowsharinglite_activation_notice' );
		}
	}

	/**
	 * Storefront install
	 * If the user activates the plugin while having a different parent theme active, prompt them to install Storefront.
	 * @since   1.0.0
	 * @return  void
	 */
	public function swowsharinglite_install_storefront_notice() {
		echo '<div class="notice is-dismissible updated">
				<p>' . __( 'Storefront Wow Sharing requires that you use Storefront as your parent theme.', 'storefront-wow-sharing-lite' ) . ' <a href="' . esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-theme&theme=storefront' ), 'install-theme_storefront' ) ) .'">' . __( 'Install Storefront now', 'storefront-wow-sharing-lite' ) . '</a></p>
			</div>';
	}

	/**
	 * Customizer Controls and settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function swowsharinglite_customize_register( $wp_customize ) {

		/**
		 * Custom controls
		 * Load custom control classes
		 */
		require_once dirname( __FILE__ ) . '/includes/class-storefront-wow-sharing-lite-control.php';


		/**
	     * Add a new section
	     */
        $wp_customize->add_section( 'swowsharinglite_section' , array(
		    'title'      	=> __( 'Storefront Wow Sharing Lite', 'storefront-wow-sharing-lite' ),
		    'description' 	=> __( '', 'storefront-wow-sharing-lite' ),
		    'priority'   	=> 35,
		) );


		/**
		 * Add a divider.
		 * Type can be set to 'text' or 'heading' to display a title or description.
		 */
		if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
			$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'swowsharinglite_gopro', array(

				'section'  	=> 'swowsharinglite_section',
				'type'		=> 'text',
				'priority' 	=> 10,
				'description' => sprintf( __( 'Enhance your social %sGo Pro%s', 'storefront-homepage-contact-section' ), '<strong><a target="_blank" href="' . esc_url( 'https://disenialia.com/store/producto/storefront-wow-sharing/'  ) . '">', '</a></strong>' ),

			) ) );
		}


		/**
		 * Show/Hide
		 */
		$wp_customize->add_setting( 'swowsharinglite_show_buttons', array(
			'default'			=> apply_filters( 'swowsharinglite_show_buttons_default', false ),
			'sanitize_callback'	=> 'absint',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swowsharinglite_show_buttons', array(
			'label'			=> __( 'Activate', 'storefront-wow-sharing-lite' ),
			'description'	=> __( 'Show/Hide Wow Sharing buttons', 'storefront-wow-sharing-lite' ),
			'section'		=> 'swowsharinglite_section',
			'settings'		=> 'swowsharinglite_show_buttons',
			'type'			=> 'checkbox',
			'priority'		=> 10,
		) ) );

		/**
		 * Add a divider.
		 * Type can be set to 'text' or 'heading' to display a title or description.
		 */
		if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
			$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'swowsharinglite_divider', array(
				'section'  	=> 'swowsharinglite_section',
				'type'		=> 'divider',
				'priority' 	=> 15,
			) ) );
		}


		/**
		 * Show/Hide Twitter
		 */
		$wp_customize->add_setting( 'swowsharinglite_show_twitter', array(
			'default'			=> apply_filters( 'swowsharinglite_show_twitter_default', false ),
			'sanitize_callback'	=> 'absint',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swowsharinglite_show_twitter', array(
			'label'			=> __( 'Twitter Button', 'storefront-wow-sharing-lite' ),
			'description'	=> __( 'Show/Hide Twitter', 'storefront-wow-sharing-lite' ),
			'section'		=> 'swowsharinglite_section',
			'settings'		=> 'swowsharinglite_show_twitter',
			'type'			=> 'checkbox',
			'priority'		=> 20,
		) ) );


		/**
		 * Select Facebook
		 */
		$wp_customize->add_setting( 'swowsharinglite_show_facebook', array(
			'default' 			=> apply_filters( 'swowsharinglite_show_facebook_default', false ),
			'sanitize_callback'	=> 'absint',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swowsharinglite_show_facebook', array(
			'label'			=> __( 'Enable Facebook', 'storefront-wow-sharing-lite' ),
			'description'	=> __( '', 'storefront-wow-sharing-lite' ),
			'section'		=> 'swowsharinglite_section',
			'settings'		=> 'swowsharinglite_show_facebook',
			'type'			=> 'checkbox',
			'priority'		=> 20,

		) ) );

		/**
		 * Select Google+
		 */
		$wp_customize->add_setting( 'swowsharinglite_show_gplus', array(
			'default' 			=> apply_filters( 'swowsharinglite_show_gplus_default', false ),
			'sanitize_callback'	=> 'absint',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swowsharinglite_show_gplus', array(
			'label'			=> __( 'Enable Google+', 'storefront-wow-sharing-lite' ),
			'description'	=> __( '', 'storefront-wow-sharing-lite' ),
			'section'		=> 'swowsharinglite_section',
			'settings'		=> 'swowsharinglite_show_gplus',
			'type'			=> 'checkbox',
			'priority'		=> 20,

		) ) );


		/**
		 * Select Email
		 */
		$wp_customize->add_setting( 'swowsharinglite_show_email', array(
			'default' 			=> apply_filters( 'swowsharinglite_show_email_default', false ),
			'sanitize_callback'	=> 'absint',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swowsharinglite_show_email', array(
			'label'			=> __( 'Enable Email', 'storefront-wow-sharing-lite' ),
			'description'	=> __( '', 'storefront-wow-sharing-lite' ),
			'section'		=> 'swowsharinglite_section',
			'settings'		=> 'swowsharinglite_show_email',
			'type'			=> 'checkbox',
			'priority'		=> 20,

		) ) );







	}

	/**
	 * Enqueue CSS and custom styles.
	 * @since   1.0.0
	 * @return  void
	 */
	public function swowsharinglite_styles() {
		wp_enqueue_style( 'swowsharinglite-styles', plugins_url( '/assets/css/style.css', __FILE__ ) );
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 *
	 * @since  1.0.0
	 */
	public function swowsharinglite_customize_preview_js() {
		wp_enqueue_script( 'swowsharinglite-customizer', plugins_url( '/assets/js/customizer.min.js', __FILE__ ), array( 'customize-preview' ), '1.1', true );
	}

	/**
	 * Storefront Wow Sharing Body Class
	 * Adds a class based on the extension name and any relevant settings.
	 */
	public function swowsharinglite_body_class( $classes ) {
		$classes[] = 'storefront-wow-sharing-lite-active';

		return $classes;
	}

	/**
	 * Layout
	 * Adjusts the default Storefront layout when the plugin is active
	 */
	public function swowsharinglite_layout_adjustments() {

		$swowsharinglite_show_buttons 	= get_theme_mod( 'swowsharinglite_show_buttons', apply_filters( 'swowsharinglite_show_buttons_default', false ) );


		$swowsharinglite_show_twitter 		= get_theme_mod( 'swowsharinglite_show_twitter', 		apply_filters( 'swowsharinglite_show_twitter_default', 		false ) );
		$swowsharinglite_show_facebook 		= get_theme_mod( 'swowsharinglite_show_facebook', 	apply_filters( 'swowsharinglite_show_facebook_default', 		false ) );
		$swowsharinglite_show_gplus 		= get_theme_mod( 'swowsharinglite_show_gplus', 		apply_filters( 'swowsharinglite_show_gplus_default', 			false ) );
		$swowsharinglite_show_email 		= get_theme_mod( 'swowsharinglite_show_email', 		apply_filters( 'swowsharinglite_show_email_default', 			false ) );

		if ( true == $swowsharinglite_show_buttons ) {

			$product_title 	= get_the_title();
			$product_url	= get_permalink();
			$product_img	= wp_get_attachment_url( get_post_thumbnail_id() );

			$email_url		= 'mailto:?subject=' . rawurlencode( $product_title ) . '&body=' . $product_url;

		?>
		<div class="storefront-wow-product-sharing">
			<ul>
				<?php if( $swowsharinglite_show_twitter == true ){ ?>
				<li><a href="javascript:(function(){window.twttr=window.twttr||{};var D=550,A=450,C=screen.height,B=screen.width,H=Math.round((B/2)-(D/2)),G=0,F=document,E;if(C>A){G=Math.round((C/2)-(A/2))}window.twttr.shareWin=window.open('http://twitter.com/share?url=<?php echo $product_url; ?>','','left='+H+',top='+G+',width='+D+',height='+A+',
personalbar=0,toolbar=0,scrollbars=1,resizable=1');E=F.createElement('script');E.src='http://platform.twitter.com/widgets.js';F.getElementsByTagName('head')[0].appendChild(E)}());
"><img src="<?php echo plugins_url( $this->token . '/assets/images/32x32-twitter.png');?>" alt="Twitter"></a></li>
				<?php } ?>

				<?php if( $swowsharinglite_show_facebook == true ){ ?>
				<li><a href="#" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(location.href), 'facebook-share-dialog','width=626,height=436'); return false;"><img src="<?php echo plugins_url( $this->token . '/assets/images/32x32-facebook.png');?>" alt="Facebook"></a></li>
				<?php } ?>

				<?php if( $swowsharinglite_show_gplus == true ){ ?>
				<li><a href="https://plus.google.com/share?url=<?php echo $product_url; ?>" onclick="javascript:window.open(this.href,
  '', 'menubar=no,toolbar=no,resizable=no,scrollbars=yes,height=600,width=600');return false;"><img src="<?php echo plugins_url( $this->token . '/assets/images/32x32-gplus.png');?>" alt="Google Plus"></a></li>
  				<?php } ?>

				<?php if( $swowsharinglite_show_email == true ){ ?>
				<li><a href="<?php echo esc_url( $email_url ); ?>" ><img src="<?php echo plugins_url( $this->token . '/assets/images/32x32-email.png');?>" alt="Email"></a></li>
				<?php } ?>


			</ul>
		</div>
<?php

		}
	}

} // End Class