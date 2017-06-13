<?php
/**
 * WPPR Admin Render Controller
 *
 * @package     WPPR
 * @subpackage  Admin
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

/**
 * Class WPPR_Admin_Render_Controller for handling page rendering.
 */
class WPPR_Admin_Render_Controller {

	/**
	 * The ID of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		// var_dump( $this->options );
	}

	/**
	 * Method to add Admin Menu Pages
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function menu_pages() {
		add_menu_page( __( 'WP Product Review', 'wp-product-review' ), __( 'Product Review', 'wp-product-review' ), 'manage_options', 'wppr', array(
			$this,
			'page_settings',
		), 'dashicons-star-half', '99.87414' );
		if ( ! defined( 'WPPR_PRO_VERSION' ) ) {
			add_submenu_page( 'wppr', __( 'More Features', 'wp-product-review' ), __( 'More Features ', 'wp-product-review' ) . '<span class="dashicons
		dashicons-star-filled" style="vertical-align:-5px; padding-left:2px; color:#FFCA54;"></span>', 'manage_options', 'wppr_pro_upsell', array(
				$this,
				'page_upsell',
			) );
		}
	}

	/**
	 * Load assets in the admin dashboard.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @param   string $hook   The name of the page hook.
	 */
	public function render_page_scripts( $hook ) {
		if ( $hook == 'toplevel_page_wppr' ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( $this->plugin_name . '-dashboard-css', WPPR_URL . '/assets/css/dashboard_styles.css', array(), $this->version );
			wp_enqueue_style( $this->plugin_name . '-admin-css', WPPR_URL . '/assets/css/admin.css', array(), $this->version );
			wp_enqueue_script( $this->plugin_name . '-tiplsy-js', WPPR_URL . '/assets/js/tipsy.js', array( 'jquery' ), $this->version );
			wp_enqueue_script( $this->plugin_name . '-admin-js', WPPR_URL . '/assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), $this->version );
		}
		if ( $hook == 'product-review_page_wppr_pro_upsell' || $hook == 'toplevel_page_wppr' ) {
			wp_enqueue_style( $this->plugin_name . '-upsell-css', WPPR_URL . '/assets/css/upsell.css', array(), $this->version );
		}
	}

	public function page_settings() {
		$this->retrive_template( 'settings' );
	}

	public function page_upsell() {
		$this->retrive_template( 'upsell' );
	}

	/**
	 * Utility method to include required layout.
	 *
	 * @since   3.0.0
	 * @access  protected
	 * @param   string $name   The name of the layout to be retrieved.
	 */
	protected function retrive_template( $name ) {
		if ( file_exists( WPPR_PATH . '/includes/admin/layouts/css/' . $name . '.css' ) ) {
			wp_enqueue_style( $this->plugin_name . '-' . $name . '-css', WPPR_URL . '/includes/admin/layouts/css/' . $name . '.css', array(), $this->version );
		}
		include_once( WPPR_PATH . '/includes/admin/layouts/' . $name . '_tpl.php' );
	}

	public function add_element( $field ) {

	    $render_helper = new WPPR_Render_Helper();
        $output = '
            <div class="controls">
				<div class="explain">' . $field['name'] . '</div>
				<p class="field_description">' . $field['description'] . '</p>
        ';
		switch ( $field['type'] ) {
			case 'input_text':

                $output .= $render_helper->add_input_text( $field );
				break;
			case 'select':
                $output .= $render_helper->add_select( $field );
				break;
			case 'color':
                $output .= $render_helper->add_color( $field );
				break;
			case 'text':
                $output .= $render_helper->add_text( $field );
				break;
		}

        $output .= '</div>';
		echo $output;

		if ( isset( $errors ) ) { return $errors; }
	}
}
