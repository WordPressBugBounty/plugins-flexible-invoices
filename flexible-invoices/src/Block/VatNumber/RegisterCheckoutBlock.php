<?php

namespace WPDesk\FlexibleInvoices\Block\VatNumber;

use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CheckoutSchema;
use Automattic\WooCommerce\StoreApi\StoreApi;
use WC_Order;
use WP_REST_Request;
use WPDeskFIVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDeskFIVendor\WPDesk_Plugin_Info;
use WPDeskFIVendor\WPDesk\Library\FlexibleInvoicesCore\Settings\Settings;

class RegisterCheckoutBlock implements Hookable {

	/**
	 * @var WPDesk_Plugin_Info
	 */
	private $plugin_info;

	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct( WPDesk_Plugin_Info $plugin_info, Settings $settings ) {
		$this->plugin_info = $plugin_info;
		$this->settings    = $settings;
	}

	public function hooks() {
		add_action( 'woocommerce_blocks_checkout_block_registration', [ $this, 'register_block' ] );
		add_action( 'woocommerce_store_api_checkout_update_order_from_request', [ $this, 'update_order_data_from_request' ], 5, 2 );
		add_action( 'woocommerce_store_api_checkout_order_processed', [ $this, 'update_user_data_from_order' ] );

		$this->extend_store();
	}

	/**
	 * Registers the actual data into each endpoint.
	 */
	public function extend_store() {
		$extend = StoreApi::container()->get( ExtendSchema::class );

		// This endpoint is used in cart for first load.
		$extend->register_endpoint_data(
			[
				'endpoint'        => CartSchema::IDENTIFIER,
				'namespace'       => BlocksIntegration::BLOCK_NAME,
				'data_callback'   => [ $this, 'get_cart_data_callback' ],
				'schema_callback' => [ $this, 'get_schema_callback' ],
				'schema_type'     => ARRAY_A,
			]
		);

		// Checkout schema is needed to save data from checkout
		$extend->register_endpoint_data(
			[
				'endpoint'        => CheckoutSchema::IDENTIFIER,
				'namespace'       => BlocksIntegration::BLOCK_NAME,
				'data_callback'   => [ $this, 'get_checkout_data_callback' ],
				'schema_callback' => [ $this, 'get_schema_callback' ],
				'schema_type'     => ARRAY_A,
			]
		);
	}

	/**
	 * @return array
	 */
	public function get_cart_data_callback(): array {
		$user_id             = get_current_user_id();
		$billing_invoice_ask = false;
		$billing_vat_number  = '';
		if ( 0 !== $user_id ) {
			$invoice_ask_meta = get_user_meta( $user_id, 'billing_invoice_ask', true );
			if ( '' === $invoice_ask_meta ) {
				$invoice_ask_meta = get_user_meta( $user_id, 'invoice_ask', true );
			}
			$billing_invoice_ask = filter_var( $invoice_ask_meta, FILTER_VALIDATE_BOOLEAN );

			$billing_vat_number = get_user_meta( $user_id, 'billing_vat_number', true );
			if ( ! is_string( $billing_vat_number ) || '' === $billing_vat_number ) {
				$billing_vat_number = get_user_meta( $user_id, 'vat_number', true );
			}
		}

		return [
			'billing_vat_number'  => $billing_vat_number,
			'billing_invoice_ask' => $billing_invoice_ask,
		];
	}

	/**
	 * @return array
	 */
	public function get_checkout_data_callback(): array {
		$invoice_ask = WC()->session->get( 'invoice_ask' );
		$vat_number  = WC()->session->get( 'vat_number' );

		$user_id = get_current_user_id();

		if ( 0 !== $user_id ) {
			if ( null === $invoice_ask || false === $invoice_ask || '' === $invoice_ask ) {
				$invoice_ask_meta = get_user_meta( $user_id, 'billing_invoice_ask', true );
				if ( '' === $invoice_ask_meta ) {
					$invoice_ask_meta = get_user_meta( $user_id, 'invoice_ask', true );
				}
				$invoice_ask = filter_var( $invoice_ask_meta, FILTER_VALIDATE_BOOLEAN );
			}

			if ( ! is_string( $vat_number ) || '' === $vat_number ) {
				$vat_number = get_user_meta( $user_id, 'billing_vat_number', true );
				if ( ! is_string( $vat_number ) || '' === $vat_number ) {
					$vat_number = get_user_meta( $user_id, 'vat_number', true );
				}
			}
		}

		return [
			'billing_invoice_ask' => (bool) $invoice_ask,
			'billing_vat_number'  => (string) $vat_number,
		];
	}

	public function update_user_data_from_order( $order ) {
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		$invoice_ask = $order->get_meta( '_billing_invoice_ask', true );
		$vat_number  = $order->get_meta( '_billing_vat_number', true );

		$user_id = $order->get_user_id();
		if ( 0 !== $user_id ) {
			update_user_meta( $user_id, 'billing_invoice_ask', (bool) $invoice_ask ? '1' : '0' );
			update_user_meta( $user_id, 'invoice_ask', $invoice_ask );

			update_user_meta( $user_id, 'billing_vat_number', $vat_number );
			update_user_meta( $user_id, 'vat_number', $vat_number );
		}
	}

	public function get_schema_callback(): array {
		return [
			'billing_vat_number'  => [
				'description' => __( 'Vat Number', 'flexible-invoices' ),
				'type'        => [ 'string', 'null' ],
				'context'     => [ 'view', 'edit' ],
			],
			'billing_invoice_ask' => [
				'description' => __( 'Invoice ask', 'flexible-invoices' ),
				'type'        => [ 'boolean', 'null' ],
				'context'     => [ 'view', 'edit' ],
			],
		];
	}

	/**
	 * Save Vat Number for order.
	 *
	 * @param WC_Order        $order
	 * @param WP_REST_Request $request
	 *
	 * @return void
	 */
	public function update_order_data_from_request( WC_Order $order, WP_REST_Request $request ) {
		$data = $request['extensions'][ BlocksIntegration::BLOCK_NAME ];
		$order->update_meta_data( '_billing_vat_number', $data['billing_vat_number'] );
		$order->update_meta_data( '_billing_invoice_ask', $data['billing_invoice_ask'] );

		$user_id = get_current_user_id();
		if ( 0 !== $user_id ) {
			update_user_meta( $user_id, 'invoice_ask', $data['billing_invoice_ask'] );
			update_user_meta( $user_id, 'vat_number', $data['billing_vat_number'] );
		}
	}

	public function register_block( $integration_registry ) {
		$integration_registry->register( new BlocksIntegration( $this->plugin_info, $this->get_block_settings() ) );
	}

	public function get_block_settings(): array {
		$invoice_ask_field = $this->settings->get_fallback( 'woocommerce_add_invoice_ask_field', 'no' ) === 'yes';
		$add_nip_field     = $this->settings->get_fallback( 'woocommerce_add_nip_field', 'no' ) === 'yes';
		$nip_label         = $this->settings->get_fallback( 'woocommerce_nip_label', esc_html__( 'Vat Number', 'flexible-invoices' ) );
		$nip_required      = $this->settings->get_fallback( 'woocommerce_nip_required', 'no' ) === 'yes';
		$validate_nip      = $this->settings->get_fallback( 'woocommerce_validate_nip', 'no' ) === 'yes';
		$shop_country      = get_option( 'woocommerce_default_country' );

		return [
			'show_ask_field'  => $invoice_ask_field,
			'show_nip_field'  => $add_nip_field,
			'description'     => '',
			'vat_input_label' => $nip_label,
			'is_required'     => $nip_required,
			'should_validate' => $validate_nip,
			'shop_country'    => $shop_country,
		];
	}
}
