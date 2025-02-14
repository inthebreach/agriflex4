<?php
/**
 * The file that provides required public DOM elements for the theme
 *
 * A class definition that inserts DOM where needed in the theme
 *
 * @link       https://github.com/AgriLife/agriflex4/blob/master/src/class-requireddom.php
 * @since      1.0.0
 * @package    agriflex4
 * @subpackage agriflex4/src
 */

namespace AgriFlex;

/**
 * Add Required DOM elements and changes
 *
 * @package AgriFlex4
 * @since 0.1.0
 */
class RequiredDOM {

	/**
	 * Instance
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Initialize the class
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function __construct() {

		// Setup Foundation.
		add_filter( 'language_attributes', array( $this, 'add_no_js_class_to_html_tag' ), 10, 2 );

		// Replace default Genesis footer.
		remove_action( 'genesis_footer', 'genesis_do_footer' );
		add_action( 'genesis_footer', array( $this, 'open_required_links_container' ), 8 );
		add_action( 'genesis_footer', array( $this, 'render_required_links' ), 10 );
		add_action( 'genesis_footer', array( $this, 'close_required_links_container' ), 12 );
		add_action( 'genesis_footer', array( $this, 'render_tamus_logo' ) );

		// Alter header tags for SEO.
		add_filter( 'genesis_seo_title', array( $this, 'alter_title_tag' ), 10, 3 );
		add_filter( 'genesis_seo_description', array( $this, 'alter_description_tag' ), 10, 3 );

		// Add Foundation mobile toggle icons.
		add_filter( 'af4_before_nav', array( $this, 'af4_nav_primary_title_bar_open' ), 9 );
		add_filter( 'af4_before_nav', array( $this, 'add_menu_toggle' ), 10 );
		add_filter( 'af4_before_nav', array( $this, 'add_search_toggle' ), 11 );
		add_filter( 'af4_before_nav', array( $this, 'af4_nav_primary_title_bar_close' ), 12 );

		// Add search form after navigation menu.
		add_action( 'genesis_header', array( $this, 'add_nav_search_widget_area' ) );

	}

	/**
	 * Return instance of class
	 *
	 * @since 0.1.0
	 * @return object.
	 */
	public static function get_instance() {

		return null === self::$instance ? new self() : self::$instance;

	}

	/**
	 * Add a no-js class where needed for Foundation styling to occur in the right order.
	 *
	 * @since 0.1.0
	 * @param string $output Properties of HTML element.
	 * @param string $doctype The doctype of the element.
	 * @return string
	 */
	public function add_no_js_class_to_html_tag( $output, $doctype ) {

		if (
			'html' !== $doctype ||
			is_admin() ||
			in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ), true )
		) {
			return $output;
		}

		$output .= ' class="no-js"';

		return $output;

	}

	/**
	 * Replace heading tag with div
	 *
	 * @param string $title The title text.
	 * @param string $inside The inner HTML.
	 * @param string $wrap The tag name of the wrap element.
	 *
	 * @return string
	 */
	public static function alter_title_tag( $title, $inside, $wrap ) {

		return preg_replace( '/\b' . $wrap . '\b/', 'div', $title );

	}

	/**
	 * Replace description tag with div
	 *
	 * @param string $title The title text.
	 * @param string $inside The inner HTML.
	 * @param string $wrap The tag name of the wrap element.
	 *
	 * @return string
	 */
	public function alter_description_tag( $title, $inside, $wrap ) {

		// $wrap may empty for some reason.
		if ( empty( $wrap ) ) {
			preg_match( '/\w+/', $title, $results );
			$wrap = $results ? $results[0] : 'h2';
		}

		// $inside may be empty for some reason.
		if ( empty( $inside ) ) {
			$results = preg_split( '/<\/?' . $wrap . '[^>]*>/', $title );
			$inside  = count( $results ) > 1 ? $results[1] : esc_attr( get_bloginfo( 'description' ) );
		}

		// Place wildcards where needed.
		$title = preg_replace( '/\b' . $wrap . '\b/', '%s', $title );
		if ( ! empty( $inside ) ) {
			$title = str_replace( $inside, '%s', $title );
		}

		// Add the site title before the description.
		$wrap  = 'div';
		$title = sprintf(
			$title,
			$wrap,
			$inside,
			$wrap
		);

		return $title;

	}

	/**
	 * Render required links open tag
	 *
	 * @since 1.0
	 * @return void
	 */
	public static function open_required_links_container() {

		echo wp_kses_post( '<div class="footer-container cell medium-auto small-12">' );

	}

	/**
	 * Render required links close tag
	 *
	 * @since 1.0
	 * @return void
	 */
	public static function close_required_links_container() {

		echo wp_kses_post( '</div>' );

	}

	/**
	 * Render required links
	 *
	 * @since 1.0
	 * @return void
	 */
	public static function render_required_links() {

		$output  = '<ul class="req-links">';
		$output .= '<li><a href="http://agrilife.tamu.edu/required-links/compact/">Compact with Texans</a></li>';
		$output .= '<li><a href="http://agrilife.tamu.edu/required-links/privacy/">Privacy and Security</a></li>';
		$output .= '<li><a href="http://itaccessibility.tamu.edu/" target="_blank">Accessibility Policy</a></li>';
		$output .= '<li><a href="https://dir.texas.gov/resource-library-item/state-website-linking-privacy-policy" target="_blank">State Link Policy</a></li>';
		$output .= '<li><a href="http://www.tsl.state.tx.us/trail" target="_blank">Statewide Search</a></li>';
		$output .= '<li><a href="http://veterans.tamu.edu/" target="_blank">Veterans Benefits</a></li>';
		$output .= '<li><a href="https://fch.tamu.edu/programs/military-programs/" target="_blank">Military Families</a></li>';
		$output .= '<li><a href="https://secure.ethicspoint.com/domain/en/report_custom.asp?clientid=19681" target="_blank">Risk, Fraud &amp; Misconduct Hotline</a></li>';
		$output .= '<li><a href="https://gov.texas.gov/organization/hsgd" target="_blank">Texas Homeland Security</a></li>';
		$output .= '<li><a href="http://veterans.portal.texas.gov/">Texas Veterans Portal</a></li>';
		$output .= '<li><a href="http://agrilifeas.tamu.edu/hr/diversity/equal-opportunity-educational-programs/" target="_blank">Equal Opportunity</a></li>';
		$output .= '<li class="last"><a href="http://agrilife.tamu.edu/required-links/orpi/">Open Records/Public Information</a></li>';
		$output .= '</ul>';

		echo wp_kses_post( $output );

	}

	/**
	 * Render TAMUS logo
	 *
	 * @since 1.0
	 * @return void
	 */
	public static function render_tamus_logo() {

		$output = '<div class="footer-container-tamus cell medium-2 small-12"><a href="http://tamus.edu/" title="Texas A&amp;M University System"><img class="footer-tamus" src="' . AF_THEME_DIRURL . '/images/footer-tamus-maroon.png" alt="Texas A&amp;M University System Member" /></a></div>';

		echo wp_kses_post( $output );

	}

	/**
	 * Add open html for af4_before filter.
	 *
	 * @since 1.3.8
	 * @param string $output Output for af4_before_nav_args.
	 * @return string
	 */
	public function af4_nav_primary_title_bar_open( $output = '' ) {

		$output .= '<div class="title-bars cell shrink title-bar-right show-for-small-only">';
		return $output;

	}

	/**
	 * Add header menu toggle
	 *
	 * @since 1.3.8
	 * @param string $output Output for af4_before_nav_args.
	 * @return string
	 */
	public function add_menu_toggle( $output = '' ) {

		$output .= '<div class="title-bar title-bar-navigation" data-responsive-toggle="nav-menu-primary"><button class="menu-icon" type="button" data-toggle="nav-menu-primary"></button><div class="title-bar-title" data-toggle="nav-menu-primary">Menu</div></div>';
		return $output;

	}

	/**
	 * Add header search toggle
	 *
	 * @since 1.3.8
	 * @param string $output Output for af4_before_nav_args.
	 * @return string
	 */
	public function add_search_toggle( $output = '' ) {

		$output .= '<div class="title-bar title-bar-search" data-responsive-toggle="header-search"><button class="search-icon" type="button" data-toggle="header-search"></button><div class="title-bar-title">Search</div></div>';

		return $output;

	}

	/**
	 * Add close html for af4_before filter.
	 *
	 * @since 1.3.8
	 * @param string $output Output for af4_before_nav_args.
	 * @return string
	 */
	public function af4_nav_primary_title_bar_close( $output = '' ) {

		$output .= '</div>';
		return $output;

	}

	/**
	 * Add header right widget area
	 *
	 * @since 1.0.6
	 * @param string $content If not empty then this function is running on a filter hook.
	 * @return string
	 */
	public static function add_nav_search_widget_area( $content ) {

		$defaults = array(
			'class' => 'cell small-12 medium-3 header-right-widget-area',
			'id'    => 'header-search',
		);
		$attr     = apply_filters( 'af4_nav_search_attr', $defaults );
		$output   = '';

		// Cycle through attributes, build tag attribute string.
		foreach ( $attr as $key => $value ) {

			if ( ! $value ) {
				continue;
			}

			if ( true === $value ) {
				$output .= esc_html( $key ) . ' ';
			} else {
				$output .= sprintf( '%s="%s" ', esc_html( $key ), esc_attr( $value ) );
			}
		}

		$before = sprintf( '<div %s>', $output );

		$wattr = apply_filters(
			'af4_nav_search_widget_area_atts',
			array(
				'before' => $before,
				'after'  => '</div>',
			)
		);

		if ( ! empty( $content ) ) {
			ob_start();
		}

		genesis_widget_area(
			'af4-header-right',
			$wattr
		);

		if ( ! empty( $content ) ) {
			$widget_area = ob_get_clean();

			return $content .= $widget_area;
		}

	}

}
