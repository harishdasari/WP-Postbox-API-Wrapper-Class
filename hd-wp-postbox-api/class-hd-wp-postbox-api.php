<?php
/*
WordPress Postbox API Class
URI: http://github.com/harishdasari
Author: Harish Dasari
Author URI: http://twitter.com/harishdasari
Version: 1.0
*/

/*=================================================================================
	WordPress Postbox API Class
 =================================================================================*/

require_once( 'class-hd-html-helper.php' );

if ( ! class_exists( 'HD_WP_Postbox_API' ) ) :
/**
 * WordPress Postboxes API Wrapper Class
 *
 * @version 1.0
 * @author  Harish Dasari
 * @link    http://github.com/harishdasari
 */
class HD_WP_Postbox_API {

	/**
	 * Holds Options for Menu Page
	 * @var array
	 */
	var $options         = array();

	/**
	 * Holds Settings fields data
	 * @var array
	 */
	var $fields          = array();

	/**
	 * Holds Section ID to addubg settings fields
	 * @var string
	 */
	var $current_section = 'default';

	/**
	 * Holds Current field data to adding settings field
	 * @var mixed
	 */
	var $current_field   = false;

	/**
	 * Holds current postbox id in the fields loop
	 * @var string
	 */
	var $current_postbox;

	/**
	 * Holds the Current Column number in the fields loop
	 * @var int
	 */
	var $current_column;

	/**
	 * Holds Menu page $hook_suffix
	 * @var boolean/string
	 */
	var $hook_suffix     = false;

	/**
	 * Holds instance of HD_HTML_Helper class
	 * @var object
	 */
	var $html_helper;

	/**
	 * Holds Current Folder Path
	 * @var string
	 */
	var $dir_path;

	/**
	 * Holds Current Folder URI
	 * @var string
	 */
	var $dir_uri;

	/**
	 * Constructor
	 *
	 * @param array $options
	 * @param array $fields
	 * @return null
	 */
	function __construct( $options = array(), $fields = array() ) {

		// Set directory path
		$this->dir_path = str_replace( '\\', '/', dirname( __FILE__ ) );

		// Set directory uri
		$this->dir_uri  = trailingslashit( home_url() ) . str_replace( str_replace( '\\', '/', ABSPATH ), '', $this->dir_path );

		// Default page options
		$options_default = array(
			'page_title'  => '',
			'menu_title'  => '',
			'menu_slug'   => '',
			'parent_slug' => '',
			'capability'  => 'manage_options',
			'icon'        => 'dashicons-admin-generic',
			'position'    => null,
			'num_columns' => 2,
			'max_columns' => 2
		);

		$this->options = wp_parse_args( $options, $options_default );

		// Check Number and Max Columns b/w 1 to 4
		foreach ( array( 'num_columns', 'max_columns' ) as $col )
			$this->options[ $col ] = ( in_array( $this->options[ $col ], range( 1, 4 ) ) ) ? absint( $this->options[ $col ] ) : 1;

		if ( $this->options['max_columns'] < $this->options['num_columns'] )
			$this->options['max_columns'] = $this->options['num_columns'];

		extract( $this->options );

		// Titles and slugs should not be empty
		if ( empty( $page_title ) || empty( $menu_title ) || empty( $menu_slug ) )
			return false;

		$this->fields  = (array) $fields;

		$this->html_helper = class_exists( 'HD_HTML_Helper' ) ? new HD_HTML_Helper : false;

		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_options' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
		add_action( 'admin_notices', array( $this, 'show_notices' ) );

	}

	/**
	 * Register a New Menu Page
	 *
	 * @return null
	 */
	function register_menu() {

		extract( $this->options );

		if ( empty( $parent_slug ) )
			$this->hook_suffix = add_menu_page( $page_title, $menu_title, $capability, $menu_slug, array( $this, 'settings_page' ), $icon, $position );
		else
			$this->hook_suffix = add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, array( $this, 'settings_page' ) );

		add_action( 'load-' . $this->hook_suffix, array( $this, 'load_options' ) );

	}

	/**
	 * Enqueue Styles and Scripts
	 *
	 * @param  string $hook_suffix
	 * @return null
	 */
	function enqueue_styles_scripts( $hook_suffix ) {

		if ( $this->hook_suffix !== $hook_suffix )
			return;

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'hd-html-helper', $this->dir_uri . '/js/admin.js', array( 'jquery', 'wp-color-picker', 'postbox' ), null, true );

	}

	/**
	 * Load Layout Options run register metabox actions
	 *
	 * @return null
	 */
	function load_options() {

		do_action( 'add_meta_boxes', $this->hook_suffix, $this->options['menu_slug'] );

		add_screen_option( 'layout_columns', array( 'max' => $this->options['max_columns'], 'default' => $this->options['num_columns'] ) );

	}

	/**
	 * Register Sections, Fields and Settings
	 *
	 * @return mull
	 */
	function register_options() {

		foreach ( $this->fields as $field_setting => $field ) {

			$field['id'] = $field_setting;

			$this->current_field = $field;

			if ( 'postbox' == $field['type'] ) {

				$field           = wp_parse_args( $field, array( 'column' => 1 ) );
				$field['column'] = (int) in_array( $field['column'], range( 1, 4 ) ) ? $field['column'] : 1;

				$this->current_postbox = $field['id'];
				$this->current_section = 'default';
				$this->current_column  = $field['column'];

				add_meta_box( $field['id'], $field['title'], array( $this, 'print_metabox' ), $this->hook_suffix, 'column' . $field['column'], 'default', $field );

			} elseif ( 'section' == $field['type'] ) {

				$this->current_section = empty( $field['id'] ) ? 'default' : $field['id'] ;

				if ( empty( $this->current_postbox ) )
					add_settings_section( $field['id'], $field['title'], array( $this, 'print_section' ), $this->options['menu_slug'] );
				else
					add_settings_section( $field['id'], $field['title'], array( $this, 'print_section' ), $this->options['menu_slug'] . '_' . $this->current_column );

			} elseif ( in_array( $field['type'], array( 'text', 'textarea', 'select', 'checkbox', 'radio', 'multiselect', 'multicheck', 'upload', 'color', 'editor' ) ) ) {

				// Set Field Value
				$field['value'] = get_option( $field['id'] );

				if ( empty( $this->current_postbox ) )
					add_settings_field( $field['id'], $field['title'], array( $this->html_helper, 'display_field' ), $this->options['menu_slug'], $this->current_section, $field );
				else
					add_settings_field( $field['id'], $field['title'], array( $this->html_helper, 'display_field' ), $this->options['menu_slug'] . '_' . $this->current_column, $this->current_section, $field );

				register_setting( $this->options['menu_slug'], $field['id'], array( $this, 'sanitize_setting' ) );

				if ( ! empty( $field['default'] ) )
					add_option( $field['id'], $field['default'] );

			}

		}

	}

	/**
	 * Show Admin Notices
	 *
	 * @return null
	 */
	function show_notices() {

		global $parent_file;

		if ( 'options-general.php' == $parent_file )
			return;

		if ( isset( $_GET['page'] ) && $_GET['page'] == $this->options['menu_slug'] )
			settings_errors();

	}

	/**
	 * Print Settings Page
	 *
	 * @return null
	 */
	function settings_page() {

		?>
		<div class="wrap <?php echo sanitize_html_class( $this->options['menu_slug'] ); ?>">

			<h2><?php echo esc_html( $this->options['page_title'] ); ?></h2>

			<form action="<?php echo admin_url( 'options.php' ) ?>" method="post">

				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php settings_fields( $this->options['menu_slug'] ); ?>

				<?php do_action( 'hd_postbox_api_page_before', $this->hook_suffix, $this->options, $this->fields ); ?>

				<table class="form-table">
					<?php do_settings_fields( $this->options['menu_slug'], 'default' ); ?>
				</table>

				<?php do_settings_sections( $this->options['menu_slug'] ); ?>

				<div id="dashboard-widgets" class="metabox-holder columns-<?php echo absint( get_current_screen()->get_columns() ); ?>">

					<?php foreach ( range( 1, get_current_screen()->get_option( 'layout_columns', 'max' ) ) as $column ) : ?>
					<div id="postbox-container-<?php echo $column; ?>" class="postbox-container">
						<?php do_meta_boxes( $this->hook_suffix, 'column' . $column, $column ); ?>
					</div>
					<?php endforeach; ?>

				</div>
				<!-- /#dashboard-widgets.metabox-holder -->

				<?php do_action( 'hd_postbox_api_page_after', $this->hook_suffix, $this->options, $this->fields ); ?>

				<div class="clear"></div>

				<?php submit_button( apply_filters( 'hd_postbox_api_save_button_text', __( 'Save Changes' ) ) ); ?>

			</form>

		</div>

		<script type="text/javascript">
		/*<![CDATA[*/
			jQuery(document).ready(function($) {
				postboxes.add_postbox_toggles( pagenow );
			});
		/*]]>*/
		</script>
		<?php

	}

	/**
	 * Print Settings Section
	 *
	 * @param  array $args Section Options
	 * @return null
	 */
	function print_section( $args ) {

		if ( isset( $this->fields[ $args['id'] ]['desc'] ) )
			echo $this->fields[ $args['id'] ]['desc'];

	}

	/**
	 * Print Metabox Content
	 *
	 * @param  int   $column Column Number
	 * @param  array $args   Metabox Arguments
	 * @return null
	 */
	function print_metabox( $column, $args ) {

		do_action( 'hd_postbox_api_metabox_before', $this->hook_suffix, $column, $this->options, $this->fields );

		echo '<table class="form-table">';
			do_settings_fields( $this->options['menu_slug'] . '_' . $column, 'default' );
		echo '</table>';

		do_settings_sections( $this->options['menu_slug'] . '_' . $column );

		do_action( 'hd_postbox_api_metabox_after', $this->hook_suffix, $column, $this->options, $this->fields );

	}

	/**
	 * Sanitize settings
	 *
	 * @param  mixed $new_value Submitted new value
	 * @return mixed            Sanitized value
	 */
	function sanitize_setting( $new_value ) {

		$setting = str_replace( 'sanitize_option_', '', current_filter() );

		$field = $this->fields[ $setting ];

		if ( ! isset( $field['sanit'] ) )
			$field['sanit'] = '';

		switch ( $field['sanit'] ) {

			case 'int' :
				return is_array( $new_value ) ? array_map( 'intval', $new_value ) : intval( $new_value );
				break;

			case 'absint' :
				return is_array( $new_value ) ? array_map( 'absint', $new_value ) : absint( $new_value );
				break;

			case 'email' :
				return is_array( $new_value ) ? array_map( 'sanitize_email', $new_value ) : sanitize_email( $new_value );
				break;

			case 'url' :
				return is_array( $new_value ) ? array_map( 'esc_url_raw', $new_value ) : esc_url_raw( $new_value );
				break;

			case 'bool' :
				return (bool) $new_value;
				break;

			case 'color' :
				return $this->sanitize_hex_color( $new_value );
				break;

			case 'html' :
				if ( current_user_can( 'unfiltered_html' ) )
					return is_array( $new_value ) ? array_map( 'wp_kses_post', $new_value ) : wp_kses_post( $new_value );
				else
					return is_array( $new_value ) ? array_map( 'wp_strip_all_tags', $new_value ) : wp_strip_all_tags( $new_value );
				break;

			case 'nohtml' :
				return is_array( $new_value ) ? array_map( 'wp_strip_all_tags', $new_value ) : wp_strip_all_tags( $new_value );
				break;

			default :
				return apply_filters( 'hd_postbox_api_sanitize_option', $new_value, $field, $setting );
				break;

		}

	}

	/**
	 * Sanitize Hex Color (taken from WP Core)
	 *
	 * @param  string $color Hex Color
	 * @return mixed         Sanitized Hex Color or null
	 */
	function sanitize_hex_color( $color ) {

		if ( '' === $color )
			return '';

		if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
			return $color;

		return null;

	}

} // HD_WP_Postbox_API end

endif; // class_exists check