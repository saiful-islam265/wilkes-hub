<?php

/**
 * Initialization of a rest api
 */
add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'data/v1',
			'/wilkes',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'get_order_from_hoodsly',
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'order_hold/v1',
			'/wilkes',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'order_on_hold_from_hub',
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'received_ccm_order/v1',
			'/wilkes',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'ccm_order_received_from_hub',
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'bol_update/v1',
			'/wilkes',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'bol_update_order_summery',
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'accessory/v1',
			'/order',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'create_accessory_order',
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'damage-claim/v1',
			'/shop-claim',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'shop_damage_claim_from_hub',
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'receive_vent/v1',
			'/delivered',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'vent_delivered',
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'receive_aftership_status/v1',
			'/delivered',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'order_status_updated_from_aftership',
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'recieve_wood_speices_list/v1',
			'/woodspecies',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'wood_species_update_from_hub',
				'permission_callback' => '__return_true',
			)
		);
	}
);

/**
 * Function to check if an order should go to wrh post type or not
 */

 function wood_species_update_from_hub( $response ) {

	$domain      = parse_url( $_SERVER['HTTP_USER_AGENT'] );
	$from_source = str_contains( $domain['path'], 'hoodslyhub' );
	$api_secret  = get_option( 'hoodslyhub_api_credentials' );
	$hash        = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );

	if ( $from_source ) {
		if ( $hash == $_SERVER['HTTP_API_SIGNATURE'] ) {
			$body_data = $response->get_body();
			$arr       = json_decode( $body_data, true );
			update_option('wilkes_Hood_Species_Options', $arr);
		}
	}
 }


/**
 * Rest api callback method
 *
 * @param [type] $response
 *
 * @return void
 * @throws Exception
 */
function get_order_from_hoodsly( $response ) {
	$domain      = parse_url( $_SERVER['HTTP_USER_AGENT'] );
	$from_source = str_contains( $domain['path'], 'hoodslyhub' );
	$api_secret  = get_option( 'hoodslyhub_api_credentials' );
	$hash        = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );

	if ( $from_source ) {
		if ( $hash == $_SERVER['HTTP_API_SIGNATURE'] ) {
			$body_data = $response->get_body();
			$arr       = json_decode( $body_data, true );

			if ( isset( $arr['type'] ) && $arr['type'] == 'order_update' ) {
				global $wpdb;
				$myposts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '%s'", '%' . $wpdb->esc_like( $arr['order_id'] ) . '%' ) );
				$post_id = $myposts[0]->ID;
				update_post_meta( $post_id, 'line_items', $arr['line_items'] );
				$updated_date        = current_time( 'mysql' );
				$order_summery       = get_post_meta( $post_id, 'order_summery', true );
				$order_summery       = ( isset( $order_summery ) && is_array( $order_summery ) ) ? $order_summery : array();
				$order_updated_hub   = array(
					array(
						'summery' => 'Order details updated from hoodslyhub ',
						'date'    => $updated_date,
					),
				);
				$order_summery_array = array_merge( $order_summery, $order_updated_hub );
				update_post_meta( $post_id, 'order_summery', $order_summery_array );
			} else {
				$estimated_shipping_date = isset( $arr['estimated_shipping_date'] ) ? sanitize_text_field( $arr['estimated_shipping_date'] ) : '';
				$bill_of_landing_id      = isset( $arr['bill_of_landing_id'] ) ? intval( $arr['bill_of_landing_id'] ) : '';
				$customer_note           = isset( $arr['data']['customer_note'] ) ? sanitize_text_field( $arr['data']['customer_note'] ) : '';
				$product_names           = isset( $arr['product_name'] ) && is_array( $arr['product_name'] ) ? $arr['product_name'] : array();

				/**
				 * Inserting manual order based on rest api request data
				 */
				$wilkes_order = array(
					'post_title'   => $arr['order_id'],
					'post_content' => $arr['order_desc'],
					'post_status'  => 'publish',
					'post_date'    => current_time( 'mysql' ),
					'post_type'    => 'wilkes_order',
				);

				$post_id = wp_insert_post( $wilkes_order );
				/**
				 * Saving order data as meta to all_orders cpt
				 */
				add_post_meta( $post_id, 'estimated_shipping_date', $estimated_shipping_date );
				add_post_meta( $post_id, 'customer_note', $customer_note );
				add_post_meta( $post_id, 'origin', $arr['origin'] );
				add_post_meta( $post_id, 'order_date', $arr['order_date'] );
				add_post_meta( $post_id, 'order_id', intval( $arr['order_id'] ) );
				add_post_meta( $post_id, 'meta_data_arr', $arr['meta_data'] );
				add_post_meta( $post_id, 'product_name', $product_names );
				add_post_meta( $post_id, 'product_cat', $arr['product_cat'] );
				add_post_meta( $post_id, 'item_sku', $arr['product_sku'] );
				add_post_meta( $post_id, 'order_status', $arr['order_status'] );
				add_post_meta( $post_id, 'custom_color_match', $arr['custom_color_match'] );
				add_post_meta( $post_id, 'billing', $arr['billing'] );
				add_post_meta( $post_id, 'shipping', $arr['shipping'] );
				add_post_meta( $post_id, 'bill_of_landing_id', $bill_of_landing_id );
				add_post_meta( $post_id, 'shipping_lines', $arr['shipping_lines'] );
				add_post_meta( $post_id, 'line_items', $arr['line_items'] );
				add_post_meta( $post_id, 'bol_pdf', $arr['bol_pdf'] );
				add_post_meta( $post_id, 'shipping_label', $arr['shipping_label'] );
				add_post_meta( $post_id, 'samples_status', $arr['samples_status'] );
				add_post_meta( $post_id, 'is_tradewinds_selected', $arr['is_tradewinds_selected'] );
				add_post_meta( $post_id, 'completion_date', $arr['completion_date'] );
				add_post_meta( $post_id, 'shipping_state', $arr['shipping_state'] );
				add_post_meta( $post_id, 'rush_manufacturing', $arr['rush_manufacturing'] );
				HoodslyHubHelper::add_order_history( $post_id, 'Order Placed on Hoodsly.com' );
				if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
					$options    = array(
						'ssl' => array(
							'verify_peer'      => false,
							'verify_peer_name' => false,
						),
					);
					$bolpdfData = file_get_contents( $arr['bol_pdf'], false, stream_context_create( $options ) );
				} else {
					$bolpdfData = file_get_contents( $arr['bol_pdf'] );
				}
				$bolpdf_name = basename( $arr['bol_pdf'] );
				$upload      = wp_upload_bits( $bolpdf_name, null, $bolpdfData );
				if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
					$options        = array(
						'ssl' => array(
							'verify_peer'      => false,
							'verify_peer_name' => false,
						),
					);
					$shipping_label = file_get_contents( $arr['shipping_label'], false, stream_context_create( $options ) );
				} else {
					$shipping_label = file_get_contents( $arr['shipping_label'] );
				}
				$shipping_label_name = basename( $arr['shipping_label'] );
				$upload              = wp_upload_bits( $shipping_label_name, null, $shipping_label );
			}
		}
	}
}

function create_accessory_order( $request ) {
	$domain      = parse_url( $_SERVER['HTTP_USER_AGENT'] );
	$from_source = str_contains( $domain['path'], 'hoodslyhub' );
	$api_secret  = get_option( 'hoodslyhub_api_credentials' );
	$hash        = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );

	if ( $from_source ) {
		if ( $hash == $_SERVER['HTTP_API_SIGNATURE'] ) {
			global $wpdb;
			$body_data = $request->get_body();
			$arr       = json_decode( $body_data, true );

			$post_args    = array(
				'post_status' => 'publish',
				'post_title'  => $arr['order_id'],
				'post_type'   => 'accessory_orders',
			);
			$accessory_id = wp_insert_post( $post_args );
			add_post_meta( $accessory_id, 'order_title', $arr['order_title'] );
			add_post_meta( $accessory_id, 'order_details', $arr['order_details'] );
			add_post_meta( $accessory_id, 'acc_order_details', $arr['acc_order_details'] );
			add_post_meta( $accessory_id, 'order_priority', $arr['order_priority'] );
			add_post_meta( $accessory_id, 'shipping_method', $arr['shipping_method'] );
			add_post_meta( $accessory_id, 'shipping_details', $arr['shipping_details'] );
			add_post_meta( $accessory_id, 'chimney_extension_MOD', $arr['chimney_extension_MOD'] );
			add_post_meta( $accessory_id, 'z_Line_solid_bottom_MOD', $arr['z_Line_solid_bottom_MOD'] );
			add_post_meta( $accessory_id, 'crown_molding_fas_sm', $arr['crown_molding_fas_sm'] );
			add_post_meta( $accessory_id, 'molding_ma_s_sm', $arr['molding_ma_s_sm'] );
			add_post_meta( $accessory_id, 'strap_q', $arr['strap_q'] );
			add_post_meta( $accessory_id, 'strapping_ma_s_sm', $arr['strapping_ma_s_sm'] );
			add_post_meta( $accessory_id, 'touch_up_fng', $arr['touch_up_fng'] );
			add_post_meta( $accessory_id, 'wood_banding_fa_s_sm', $arr['wood_banding_fa_s_sm'] );
			add_post_meta( $accessory_id, 'additional_notes', $arr['additional_notes'] );
			add_post_meta( $accessory_id, 'add_fees', $arr['add_fees'] );
			add_post_meta( $accessory_id, 'customer_email', $arr['customer_email'] );
			add_post_meta( $accessory_id, 'attachment_file_url', $arr['attachment_file_url'] );
			add_post_meta( $accessory_id, 'order_status', $arr['order_status'] );
		}
	}
}

function vent_delivered( $request ) {
	$domain      = parse_url( $_SERVER['HTTP_USER_AGENT'] );
	$from_source = str_contains( $domain['path'], 'hoodslyhub' );
	$api_secret  = get_option( 'hoodslyhub_api_credentials' );
	$hash        = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );

	if ( $from_source ) {
		if ( $hash == $_SERVER['HTTP_API_SIGNATURE'] ) {
			global $wpdb;
			$body_data    = $request->get_body();
			$arr          = json_decode( $body_data, true );
			$shop_post_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT ID FROM $wpdb->posts WHERE post_name = %d AND post_type = %s",
					$arr['order_title'],
					'wilkes_order',
				)
			);
			update_post_meta( $shop_post_id, 'action', 'Delivered' );
		}
	}
}

function bol_update_order_summery( $request ) {
	$domain      = parse_url( $_SERVER['HTTP_USER_AGENT'] );
	$from_source = str_contains( $domain['path'], 'hoodslyhub' );
	$api_secret  = get_option( 'hoodslyhub_api_credentials' );
	$hash        = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );

	if ( $from_source ) {
		if ( $hash == $_SERVER['HTTP_API_SIGNATURE'] ) {
			global $wpdb;
			$body_data           = $request->get_body();
			$arr                 = json_decode( $body_data, true );
			$bol_summery         = $arr['bol_summery'];
			$order_id            = $arr['order_id'];
			$shop_post_id        = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT ID FROM $wpdb->posts WHERE post_name = %d AND post_type = %s",
					$order_id,
					'wilkes_order',
				)
			);
			$order_summery       = get_post_meta( $shop_post_id, 'order_summery', true );
			$order_summery       = ( isset( $order_summery ) && is_array( $order_summery ) ) ? $order_summery : array();
			$order_summery_array = array_merge( $order_summery, $bol_summery );
			update_post_meta( $shop_post_id, 'order_summery', $order_summery_array );
			HoodslyHubHelper::add_order_history( $shop_post_id, $order_summery_array );
			update_post_meta( $shop_post_id, 'bol_regenerated', 'yes' );
			update_post_meta( $shop_post_id, 'shipping', $arr['shipping'] );
		}
	}
}

/**
 * @param $request
 *
 * @return void
 */
function order_status_updated_from_aftership( $request ) {
	$domain      = parse_url( $_SERVER['HTTP_USER_AGENT'] );
	$from_source = str_contains( $domain['path'], 'hoodslyhub' );
	$api_secret  = get_option( 'hoodslyhub_api_credentials' );
	$hash        = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );

	if ( $from_source ) {
		if ( $hash == $_SERVER['HTTP_API_SIGNATURE'] ) {
			global $wpdb;
			$body_data    = $request->get_body();
			$arr          = json_decode( $body_data, true );
			$post_id      = $arr['post_id'];
			$order_status = $arr['order_status'];
			HoodslyHubHelper::add_order_history( $post_id, 'Order successfully delivered' );
			update_post_meta( $post_id, 'order_status', $order_status );

		}
	}
}
