<?php

namespace WPDesk\FlexibleInvoices\Tracker;

/**
 * Tracks data about usages.
 *
 * @package WPDesk\ShopMagic\Tracker
 */
class Tracker {

	public static $script_version = '11';

	/** @var string */
	private $plugin_file_name;

	public function __construct( $plugin_file_name ) {
		$this->plugin_file_name = $plugin_file_name;
	}

	public function hooks() {
		add_filter( 'wpdesk_tracker_notice_screens', [ $this, 'wpdesk_tracker_notice_screens' ] );
		add_filter( 'wpdesk_track_plugin_deactivation', [ $this, 'wpdesk_track_plugin_deactivation' ] );
		add_action( 'activated_plugin', [ $this, 'activated_plugin' ], 10, 2 );
	}

	public function wpdesk_track_plugin_deactivation( $plugins ) {
		$plugins[ $this->plugin_file_name ] = $this->plugin_file_name;

		return $plugins;
	}

	public function wpdesk_tracker_notice_screens( $screens ) {
		$current_screen = get_current_screen();

		if ( $current_screen->id === 'edit-inspire_invoice' || $current_screen->id === 'inspire_invoice_page_invoices_settings' ) {
			$screens[] = $current_screen->id;
		}

		return $screens;
	}

	public function activated_plugin( $plugin, $network_wide ) {
		if ( $network_wide ) {
			return;
		}
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return;
		}
		if ( ! $this->wpdesk_tracker_enabled() ) {
			return;
		}
		if ( $plugin === $this->plugin_file_name ) {
			$options = get_option( 'wpdesk_helper_options', [] );

			if ( empty( $options ) ) {
				$options = [];
			}
			if ( empty( $options['wpdesk_tracker_agree'] ) ) {
				$options['wpdesk_tracker_agree'] = '0';
			}
			$wpdesk_tracker_skip_plugin = get_option( 'wpdesk_tracker_skip_flexible_invoices_woocommerce', '0' );
			if ( $options['wpdesk_tracker_agree'] === '0' && $wpdesk_tracker_skip_plugin === '0' ) {
				update_option( 'wpdesk_tracker_notice', '1' );
				update_option( 'wpdesk_tracker_skip_flexible_invoices_woocommerce', '1' );
				if ( ! apply_filters( 'wpdesk_tracker_do_not_ask', false ) ) {
					wp_redirect( admin_url( 'admin.php?page=wpdesk_tracker&plugin=' . $this->plugin_file_name ) ); //phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
					exit;
				}
			}
		}
	}

	/**
	 * @return bool
	 */
	private function wpdesk_tracker_enabled() {
		$tracker_enabled = true;
		if ( ! empty( $_SERVER['SERVER_ADDR'] ) && $_SERVER['SERVER_ADDR'] === '127.0.0.1' ) {
			$tracker_enabled = false;
		}

		return (bool) apply_filters( 'wpdesk_tracker_enabled', $tracker_enabled );
	}
}
