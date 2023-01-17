<?php
/**
 * get permalink by template name
 *
 * @param $temp
 *
 * @return string|null
 */
function get_template_link( $temp ) {
	$link  = null;
	$pages = get_pages(
		array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => $temp,
		)
	);
	if ( isset( $pages[0] ) ) {
		$link = get_page_link( $pages[0]->ID );
	}

	return $link;
}

// Theme icon field
if ( class_exists( 'acf_field' ) ) {
	class acf_field_theme_icon extends acf_field {
		function __construct() {
			// vars
			$this->name     = 'theme_icons_field';
			$this->label    = __( 'Theme Icons', 'hoodslyhub' );
			$this->category = __( 'Choice', 'hoodslyhub' );
			parent::__construct();
		}

		function render_field( $field ) {
			$choices             = array(
				'icon-file'            => '<i class="icon-file"></i> File',
				'icon-help'            => '<i class="icon-help"></i> Help',
				'icon-home'            => '<i class="icon-home"></i> Home',
				'icon-line-chart'      => '<i class="icon-line-chart"></i> Line Chart',
				'icon-message'         => '<i class="icon-message"></i> Message',
				'icon-notifications'   => '<i class="icon-notifications"></i> Notifications',
				'icon-paste'           => '<i class="icon-paste"></i> Paste',
				'icon-setting'         => '<i class="icon-setting"></i> Setting',
				'icon-setting-sliders' => '<i class="icon-setting-sliders"></i> Setting Sliders',
				'icon-users'           => '<i class="icon-users"></i> Users',
				'icon-angle-down'      => '<i class="icon-angle-down"></i> Angle Down',
				'icon-angle-left'      => '<i class="icon-angle-left"></i> Angle Left',
				'icon-angle-right'     => '<i class="icon-angle-right"></i> Angle Right',
				'icon-angle-up'        => '<i class="icon-angle-up"></i> Angle Up',
				'icon-calendar'        => '<i class="icon-calendar"></i> Calendar',
				'icon-caret-down'      => '<i class="icon-caret-down"></i> Caret Down',
				'icon-caret-up'        => '<i class="icon-caret-up"></i> Caret Up',
				'icon-close'           => '<i class="icon-close"></i> Close',
				'icon-email'           => '<i class="icon-email"></i> Email',
				'icon-email-open'      => '<i class="icon-email-open"></i> Email Open',
			);
			$field['choices']    = $choices;
			$field['type']       = 'select';
			$field['ui']         = 1;
			$field['allow_null'] = 0;
			$field['ajax']       = 0;
			$field['multiple']   = 0;
			echo "<div class='acf-field acf-field-select acf-field-select-icon' 
           data-name='{$field['label']}' 
           data-type='select' 
           data-key='{$field['key']}'>";
			acf_render_field( $field );
			echo '</div>';
		}

		function format_value( $value ) {
			if ( ! $value || empty( $value ) ) {
				return false;
			}

			return $value;
		}
	}

	new acf_field_theme_icon();
}


/**
 * Order Delete Method
 */
function wilkeshub_delete_order() {
	$permission = check_ajax_referer( 'wilkeshub_delete_order_nonce', 'nonce', false );
	if ( false === $permission ) {
		wp_die();
	} else {
		wp_delete_post( $_REQUEST['id'] );
	}
}

add_action( 'wp_ajax_wilkeshub_delete_order', 'wilkeshub_delete_order' );

/**
 * Ventilation Tracling
 *
 * @return void
 */
function add_ventilation_tracking() {
	$email   = $_POST['vent_email'];
	$to      = array(
		$email,
	);
	$headers = 'From: Shipping Date <support@hoodslyhub.com>' . "\r\n";
	$subject = 'Damage Claim for #';
	if ( defined( 'WP_DEBUG' ) ) {
		$message = 'Damage Claim URL:<a href="http:///damage-claim">Damage Claim</a>';
	} else {
		$message = 'Damage Claim URL:<a href="https:///damage-claim">Damage Claim</a>';
	}
	wp_mail( $to, $subject, $message, $headers );
	$post_id       = $_POST['post_id'];
	$order_summery = get_post_meta( $post_id, 'order_summery', true );
	$dvent_date    = current_time( 'mysql' );
	update_post_meta(
		$post_id,
		'order_summery',
		array_merge(
			$order_summery,
			array(
				array(
					'summery' => 'Ventilation Tracking has been submitted',
					'date'    => $dvent_date,
				),
			)
		)
	);
}

add_action( 'wp_ajax_add_ventilation_tracking', 'add_ventilation_tracking' );

/**
 * Order Hold request from HUB
 *
 * @param $request
 *
 * @return void
 */
function order_on_hold_from_hub( $request ) {
	global $wpdb;
	$response_data = json_decode( $request->get_body(), true );
	$order_id      = $response_data['order_id'];
	$myposts       = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '%s'", '%' . $wpdb->esc_like( $order_id ) . '%' ) );
	$post_id       = $myposts[0]->ID;
	update_post_meta( $post_id, 'order_status', 'Order Hold' );
}

/**
 * request ventilation
 */
function request_ventilation() {
	$permission = check_ajax_referer( 'request_ventilation_nonce', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Nonce error',
			)
		);
		wp_die();
	} else {
		$post_id             = filter_input( INPUT_POST, 'postid', FILTER_SANITIZE_NUMBER_INT );
		$order_id             = filter_input( INPUT_POST, 'orderid', FILTER_SANITIZE_STRING );
		$vent_request_data             = wp_json_encode(
			array(
				'order_id' => $order_id,
			)
		);
		$api_endpoint                  = get_option( 'hoodslyhub_api_settings' );
		$ventilation_request_end_point = '';
		foreach ( $api_endpoint['hub_order_status_endpoint']['feed'] as $key => $value ) {
			if ( 'ventilation_request_end_point' === $value['end_point_type'] ) {
				$ventilation_request_end_point = $value['end_point_url'];
			}
		}
		$api_secret    = get_option( 'hoodslyhub_api_credentials' );
		$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
		wp_remote_post(
			$ventilation_request_end_point,
			array(
				'body'    => $vent_request_data,
				'headers' => array(
					'content-type'  => 'application/json',
					'Api-Signature' => $api_signature,
				),
			)
		);

		update_post_meta( $post_id, 'is_tradewinds_selected', 'yes' );
		update_post_meta( $post_id, 'ventilation_requested', 'yes' );

		/*Order History updated*/
		HoodslyHubHelper::add_order_history( $post_id, 'Ventilation Requested' );
		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Ventilation Requested',
			)
		);
	}
}

add_action( 'wp_ajax_request_ventilation', 'request_ventilation' );

/**
 * wilkes Custom color match status action - received
 */
function wilkes_ccm_received_action() {
	$permission = check_ajax_referer( 'wilkes_received_nonce', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Nonce error',
			)
		);
		wp_die();
	} else {
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		update_post_meta( $post_id, 'samples_status', 'Received' );

		/*Order History updated*/
		HoodslyHubHelper::add_order_history( $post_id, 'Received custom color match samples' );
		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Received custom color match sample.',
			)
		);
	}
}

add_action( 'wp_ajax_wilkes_ccm_received_action', 'wilkes_ccm_received_action' );

/**
 * wilkes Custom color match samples Send To Be Matched - received
 *
 * @return void
 */
function wilkes_ccm_send_to_be_matched_action() {
	$permission = check_ajax_referer( 'ccm_sent_to_be_matched_nonce', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Send To Be Matched Nonce error',
			)
		);
		wp_die();
	} else {
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		update_post_meta( $post_id, 'samples_status', 'Send To Be Matched' );

		/*Order History updated*/
		HoodslyHubHelper::add_order_history( $post_id, 'Sample has been sent to be matched' );

		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Sample has been sent to be matched',
			)
		);
	}
}

add_action( 'wp_ajax_wilkes_ccm_send_to_be_matched_action', 'wilkes_ccm_send_to_be_matched_action' );

/**
 * wilkes Custom color match samples matched action
 *
 * @return void
 */
function wilkes_ccm_matched_action() {
	$permission = check_ajax_referer( 'ccm_matched_nonce', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Samples Matched Nonce error',
			)
		);
		wp_die();
	} else {
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		update_post_meta( $post_id, 'samples_status', 'Matched' );
		update_post_meta( $post_id, 'custom_color_match', '0' );
		update_post_meta( $post_id, 'completion_date', 'none' );
		update_post_meta( $post_id, 'order_status', 'In Production' );

		//Data Send to Hub
		$order_status                    = get_post_meta( $post_id, 'order_status', true );
		$order_id                        = get_post_meta( $post_id, 'order_id', true );
		$hub_data                        = wp_json_encode(
			array(
				'order_status' => $order_status,
				'order_id'     => $order_id,
				'shop'         => 'Wilkes',
			)
		);
		$api_endpoint                    = get_option( 'hoodslyhub_api_settings' );
		$ccm_order_status_update_request = '';
		foreach ( $api_endpoint['hub_order_status_endpoint']['feed'] as $key => $value ) {
			if ( 'ccm_order_status_update_request' === $value['end_point_type'] ) {
				$ccm_order_status_update_request = $value['end_point_url'];
			}
		}
		$api_secret    = get_option( 'hoodslyhub_api_credentials' );
		$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
		$data          = wp_remote_post(
			$ccm_order_status_update_request,
			array(
				'body'    => $hub_data,
				'headers' => array(
					'content-type'  => 'application/json',
					'Api-Signature' => $api_signature,
				),
			)
		);
		/*Order History updated*/
		HoodslyHubHelper::add_order_history( $post_id, 'Sample Successfully Matched' );
		HoodslyHubHelper::add_order_history( $post_id, 'Order In Production' );

		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Sample Successfully Matched',
			)
		);
	}
}

add_action( 'wp_ajax_wilkes_ccm_matched_action', 'wilkes_ccm_matched_action' );


/**
 * Custom color match order from hub - API request
 *
 * @param $response
 *
 * @return void
 */
function ccm_order_received_from_hub( $response ) {
	$api_secret = get_option( 'hoodslyhub_api_credentials' );
	$hash       = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );

	if ( $_SERVER['HTTP_API_SIGNATURE'] === $hash ) {
		global $wpdb;
		$body_data         = $response->get_body();
		$arr               = json_decode( $body_data, true );
		$order_id          = $arr['order_id'];
		$ld_samples_status = $arr['ld_samples_status'];
		$order_status      = $arr['order_status'];
		$wilkes_post_id    = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE post_name = %d AND post_type = %s",
				$order_id,
				'wilkes_order',
			)
		);
		update_post_meta( $wilkes_post_id, 'custom_color_match_status', 'Delivered' );
		update_post_meta( $wilkes_post_id, 'samples_status', $ld_samples_status );
		update_post_meta( $wilkes_post_id, 'order_status', $order_status );
	}
}

/**
 * wilkes Pending Order to Production
 */
function wilkes_order_pending_to_production() {
	$permission = check_ajax_referer( 'wilkes_order_pending_to_production_nonce', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Nonce error',
			)
		);
		wp_die();
	} else {
		$post_id  = $_REQUEST['post_id'];
		$order_id = $_REQUEST['order_id'];
		update_post_meta( $post_id, 'order_status', 'In Production' );
		update_post_meta( $post_id, 'completion_date', 'none' );
		//Data Send to Hub
		$order_status           = get_post_meta( $post_id, 'order_status', true );
		$hub_data               = wp_json_encode(
			array(
				'order_status' => $order_status,
				'order_id'     => $order_id,
			)
		);
		$api_endpoint           = get_option( 'hoodslyhub_api_settings' );
		$order_status_end_point = '';
		foreach ( $api_endpoint['hub_order_status_endpoint']['feed'] as $key => $value ) {
			if ( 'order_status_end_point' === $value['end_point_type'] ) {
				$order_status_end_point = $value['end_point_url'];
			}
		}
		$api_secret    = get_option( 'hoodslyhub_api_credentials' );
		$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
		wp_remote_post(
			$order_status_end_point,
			array(
				'body'    => $hub_data,
				'headers' => array(
					'content-type'  => 'application/json',
					'Api-Signature' => $api_signature,
				),
			)
		);

		/*Order History updated*/
		HoodslyHubHelper::add_order_history( $post_id, 'Order Received & Start to Production' );

		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Order Received & Start to Production.',
			)
		);
	}
}

add_action( 'wp_ajax_wilkes_order_pending_to_production', 'wilkes_order_pending_to_production' );

/**
 * Damage CLiam request form Hub
 */
function shop_damage_claim_from_hub( $response ) {
	global $wpdb;
	$body_data          = $response->get_body();
	$arr                = json_decode( $body_data, true );
	$order_id           = $arr['order_id'];
	$replacement_option = $arr['replacement_option'];
	$post_id            = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM $wpdb->posts WHERE post_name = %d AND post_type = %s",
			$order_id,
			'wilkes_order',
		)
	);
	update_post_meta( $post_id, 'shop_claim_type', $arr['shop_claim_type'] );
	update_post_meta( $post_id, 'shop_claim', $arr['shop_claim'] );
	update_post_meta( $post_id, 'claim_value', $arr['claim_value'] );
	update_post_meta( $post_id, 'refund_description', $arr['refund_description'] );
	update_post_meta( $post_id, 'refund_value', $arr['refund_value'] );
	update_post_meta( $post_id, 'shop_claim_details', $arr['shop_claim_details'] );

	update_post_meta( $post_id, 'damage_claim_id', $arr['damage_claim_id'] );
	update_post_meta( $post_id, 'damage_type', $arr['damage_type'] );
	update_post_meta( $post_id, 'damage_details', $arr['damage_details'] );
	update_post_meta( $post_id, 'damage_claim_filling_date', $arr['damage_claim_filling_date'] );
	update_post_meta( $post_id, 'damage_proof_submit_date', $arr['damage_proof_submit_date'] );
	update_post_meta( $post_id, 'damage_proof_submit_date', $arr['damage_proof_submit_date'] );

	foreach ( $arr['image_src'] as $key => $value ) {
		if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
			$options    = array(
				'ssl' => array(
					'verify_peer'      => false,
					'verify_peer_name' => false,
				),
			);
			$image_data = file_get_contents( $value[0], false, stream_context_create( $options ) );
		} else {
			$image_data = file_get_contents( $value[0] );
		}
		$image_name               = basename( $value[0] );
		$upload                   = wp_upload_bits( $image_name, null, $image_data );
		$arr['image_src'][ $key ] = $upload['url'];

	}
	update_post_meta( $post_id, 'damage_image_src', $arr['image_src'] );

	if ( 'Yes' === $replacement_option ) {
		$hood_replace      = $arr['hood_replace'];
		$f_shelf_replace   = $arr['f_shelf_replace'];
		$hall_tree_replace = $arr['hall_tree_replace'];
		update_post_meta( $post_id, 'hood_replace', $hood_replace );
		update_post_meta( $post_id, 'f_shelf_replace', $f_shelf_replace );
		update_post_meta( $post_id, 'hall_tree_replace', $hall_tree_replace );

		$get_hood_replace      = get_post_meta( $post_id, 'hood_replace', true );
		$get_f_shelf_replace   = get_post_meta( $post_id, 'f_shelf_replace', true );
		$get_hall_tree_replace = get_post_meta( $post_id, 'hall_tree_replace', true );

		if ( 'miscellaneous' === $get_hood_replace || 'miscellaneous' === $get_f_shelf_replace || 'miscellaneous' === $get_hall_tree_replace ) {
			$miscellaneous = ( isset( $arr['miscellaneous'] ) && ! empty( $arr['miscellaneous'] ) ) ? $arr['miscellaneous'] : 'N/A';
			update_post_meta( $post_id, 'miscellaneous', $miscellaneous );
		}
	} else {
		$no_replace = $arr['no_replace'];
		update_post_meta( $post_id, 'no_replace', $no_replace );
	}
	HoodslyHubHelper::add_order_history( $post_id, 'Damage claim received from HUB' );
}

/**
 *  Shop claim approving Method
 */
function shop_claim_approved_request() {
	$permission = check_ajax_referer( 'shop_claim_approved_nonce', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Nonce Error',
			)
		);
		wp_die();
	} else {
		global $wpdb;
		$post_id = $_POST['post_id'];
		update_post_meta( $post_id, 'shop_claim', 'approved' );
		HoodslyHubHelper::add_order_history( $post_id, 'Claim Approved' );

		$bill_of_landing_id        = intval( get_post_meta( $post_id, 'bill_of_landing_id', true ) );
		$shipping                  = get_post_meta( $post_id, 'shipping', true );
		$billing                   = get_post_meta( $post_id, 'billing', true );
		$customer_note             = get_post_meta( $post_id, 'customer_note', true );
		$order_desc                = $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM $wpdb->posts WHERE ID = %d AND post_type = %s", $post_id, 'wilkes_order' ) );
		$order_status              = trim( get_post_meta( $post_id, 'order_status', true ) );
		$order_date                = get_post_meta( $post_id, 'order_date', true );
		$es_shipping_date          = get_post_meta( $post_id, 'estimated_shipping_date', true );
		$origin                    = get_post_meta( $post_id, 'origin', true );
		$meta_data_arr             = get_post_meta( $post_id, 'meta_data_arr', true );
		$product_names             = get_post_meta( $post_id, 'product_name', true );
		$product_cat               = get_post_meta( $post_id, 'product_cat', true );
		$product_cat_name          = get_post_meta( $post_id, 'product_cat_name', true );
		$tradewinds_quickship      = get_post_meta( $post_id, 'tradewinds_quickship', true );
		$product_sku               = get_post_meta( $post_id, 'item_sku', true );
		$custom_color_match        = get_post_meta( $post_id, 'custom_color_match', true );
		$shipping_lines            = get_post_meta( $post_id, 'shipping_lines', true );
		$order_id                  = get_post_meta( $post_id, 'order_id', true );
		$line_items                = get_post_meta( $post_id, 'line_items', true );
		$bol_pdf                   = get_post_meta( $post_id, 'bol_pdf', true );
		$shipping_label            = get_post_meta( $post_id, 'shipping_label', true );
		$samples_status            = get_post_meta( $post_id, 'shipping_label', true );
		$is_tradewinds_selected    = get_post_meta( $post_id, 'is_tradewinds_selected', true );
		$completion_date           = get_post_meta( $post_id, 'completion_date', true );
		$bol_regenerated           = get_post_meta( $post_id, 'bol_regenerated', true );
		$damage_claim_id           = get_post_meta( $post_id, 'damage_claim_id', true );
		$damage_item               = get_post_meta( $post_id, 'damage_item', true );
		$damage_type               = get_post_meta( $post_id, 'damage_type', true );
		$damage_details            = get_post_meta( $post_id, 'damage_details', true );
		$damage_claim_filling_date = get_post_meta( $post_id, 'damage_claim_filling_date', true );
		$damage_proof_submit_date  = get_post_meta( $post_id, 'damage_proof_submit_date', true );
		$claim_value               = get_post_meta( $post_id, 'claim_value', true );
		$hood_replace              = get_post_meta( $post_id, 'hood_replace', true );
		$f_shelf_replace           = get_post_meta( $post_id, 'f_shelf_replace', true );
		$hall_tree_replace         = get_post_meta( $post_id, 'hall_tree_replace', true );
		$no_replace                = get_post_meta( $post_id, 'no_replace', true );
		$damage_image_src          = get_post_meta( $post_id, 'damage_image_src', true );

		/**
		 * Inserting manual order based on rest api request data
		 */
		$wilkes_order = array(
			'post_title'   => 'REP-' . $order_id,
			'post_content' => $order_desc,
			'post_status'  => 'publish',
			'post_date'    => current_time( 'mysql' ),
			'post_type'    => 'wilkes_order',
		);

		$post_id = wp_insert_post( $wilkes_order );
		/**
		 * Saving order data as meta to all_orders cpt
		 */

		add_post_meta( $post_id, 'estimated_shipping_date', $es_shipping_date );
		add_post_meta( $post_id, 'customer_note', $customer_note );
		add_post_meta( $post_id, 'origin', $origin );
		add_post_meta( $post_id, 'order_date', $order_date );
		add_post_meta( $post_id, 'order_id', 'REP-' . $order_id );
		add_post_meta( $post_id, 'meta_data_arr', $meta_data_arr );
		add_post_meta( $post_id, 'product_name', $product_names );
		add_post_meta( $post_id, 'product_cat', $product_cat );
		add_post_meta( $post_id, 'product_cat_name', $product_cat_name );
		add_post_meta( $post_id, 'tradewinds_quickship', $tradewinds_quickship );
		add_post_meta( $post_id, 'item_sku', $product_sku );
		add_post_meta( $post_id, 'order_status', $order_status );
		add_post_meta( $post_id, 'custom_color_match', $custom_color_match );
		add_post_meta( $post_id, 'billing', $billing );
		add_post_meta( $post_id, 'shipping', $shipping );
		add_post_meta( $post_id, 'bill_of_landing_id', $bill_of_landing_id );
		add_post_meta( $post_id, 'shipping_lines', $shipping_lines );
		add_post_meta( $post_id, 'line_items', $line_items );
		add_post_meta( $post_id, 'bol_pdf', $bol_pdf );
		add_post_meta( $post_id, 'shipping_label', $shipping_label );
		add_post_meta( $post_id, 'samples_status', $samples_status );
		add_post_meta( $post_id, 'is_tradewinds_selected', $is_tradewinds_selected );
		add_post_meta( $post_id, 'completion_date', $completion_date );
		add_post_meta( $post_id, 'bol_regenerated', $bol_regenerated );
		add_post_meta( $post_id, 'is_priority', 'yes' );
		add_post_meta( $post_id, 'damage_claim_id', $damage_claim_id );
		add_post_meta( $post_id, 'damage_item', $damage_item );
		add_post_meta( $post_id, 'damage_type', $damage_type );
		add_post_meta( $post_id, 'damage_details', $damage_details );
		add_post_meta( $post_id, 'damage_claim_filling_date', $damage_claim_filling_date );
		add_post_meta( $post_id, 'damage_proof_submit_date', $damage_proof_submit_date );
		add_post_meta( $post_id, 'damage_image_src', $damage_image_src );
		add_post_meta( $post_id, 'claim_value', $claim_value );
		add_post_meta( $post_id, 'hood_replace', $hood_replace );
		add_post_meta( $post_id, 'f_shelf_replace', $f_shelf_replace );
		add_post_meta( $post_id, 'hall_tree_replace', $hall_tree_replace );
		add_post_meta( $post_id, 'no_replace', $no_replace );
		add_post_meta( $post_id, 'hood_replace', $hood_replace );
		add_post_meta( $post_id, 'f_shelf_replace', $f_shelf_replace );
		add_post_meta( $post_id, 'hall_tree_replace', $hall_tree_replace );
		add_post_meta( $post_id, 'no_replace', $no_replace );

		//$order_date = get_post_meta( $post_id, 'order_date', true );
		HoodslyHubHelper::add_order_history( $post_id, 'Claim Approved' );
		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'success',
			)
		);
	}
}

add_action( 'wp_ajax_shop_claim_approved_request', 'shop_claim_approved_request' );

/**
 * Update order status bulk
 *
 * @return void
 */
function update_order_status_bulk() {
	if ( ! empty( $_POST['orderid_array'] ) ) {
		$postid_array  = $_POST['postid_array'];
		$orderid_array = $_POST['orderid_array'];

		foreach ( $postid_array as $value ) {
			update_post_meta( $value, 'order_status', $_POST['status_label'] );
			$product_cat = get_post_meta( $value, 'product_cat', true );
			/* if($product_cat[0] == 'floating-shelves' && $_POST['status_label'] == 'Shipped'){
				$ups_gen = new UPSShippingLabel();
				$ups_gen->create_shipping_label_dir();
				$ups_gen->create_shipping_label( $value );
			} */
		}

		/* $pdf = new Clegginabox\PDFMerger\PDFMerger;

		$pdf->addPDF('http://www.africau.edu/images/default/sample.pdf');
		$pdf->addPDF('https://www.clickdimensions.com/links/TestPDFfile.pdf');

		$pdf->merge('file', 'http://wilkeshub.test/wp-content/uploads/bol/merged-bulk-bol-15827267.pdf', 'P'); */

		$wilkes_data                 = wp_json_encode(
			array(
				'order_id'     => $orderid_array,
				'order_status' => $_POST['status_label'],
			)
		);
		$api_endpoint                = get_option( 'hoodslyhub_api_settings' );
		$bulk_order_status_end_point = '';
		foreach ( $api_endpoint['hub_order_status_endpoint']['feed'] as $key => $value ) {
			if ( 'bulk_status_update_end_point' === $value['end_point_type'] ) {
				$bulk_order_status_end_point = $value['end_point_url'];
			}
		}
		$api_secret    = get_option( 'hoodslyhub_api_credentials' );
		$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
		wp_remote_post(
			$bulk_order_status_end_point,
			array(
				'body'    => $wilkes_data,
				'headers' => array(
					'content-type'  => 'application/json',
					'Api-Signature' => $api_signature,
				),
			)
		);
		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Update Successfully',
			)
		);
	}
}

add_action( 'wp_ajax_update_order_status_bulk', 'update_order_status_bulk' );

/**
 * print_order_details
 *
 * @return void
 */
function print_order_details() {
	$permission = check_ajax_referer( 'print_details', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Nonce error',
			)
		);
		wp_die();
	} else {
		$orderid = $_REQUEST['orderid'];

		$args  = array(
			'post_type' => 'wilkes_order',
			's'         => $orderid,
		);
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) :
				$query->the_post();
				?>
				<div class="dashboard-page__order-detail-body dashboard-page__order-detail-body-2">
					<?php
					$line_items = get_post_meta( get_the_ID(), 'line_items', true );
					foreach ( $line_items['line_items'] as $line_item ) :
						$product_name_color = $line_item['product_name'];
						$product_name_split = explode( '-', $product_name_color );
						$product_name       = $product_name_split[0];
						?>
						<div class="product-info" style="margin: 100px;padding: 0; width: auto;">
							<div class="text">
								<ul class="list-style">
									<li>
										<strong>Product Name</strong>
										<span>: <?php echo esc_html( $product_name ); ?></span>
									</li>

									<?php if ( 'quick-shipping' === $line_item['product_cat'] ) : ?>
										<li>
											<strong>Product ID</strong>
											<span>: #<?php echo intval( $line_item['product_id'] ); ?></span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['color']['value'] && $line_item['color']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Color', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo esc_html( $line_item['color']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['size']['value'] && $line_item['size']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Size', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['size']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['trim_options']['value'] && $line_item['trim_options']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Trim Options', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['trim_options']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['remove_trim']['value'] && $line_item['remove_trim']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'How Would You Like Your Trim?', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['remove_trim']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['crown_molding']['value'] && $line_item['crown_molding']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Crown Molding', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['crown_molding']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['increase_depth']['value'] && $line_item['increase_depth']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Increase Depth', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['increase_depth']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['reduce_height']['value'] && $line_item['reduce_height']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Reduce Height', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['reduce_height']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['extend_chimney']['value'] && $line_item['extend_chimney']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Extend Your Chimney', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['extend_chimney']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['solid_button']['value'] && $line_item['solid_button']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Add A Solid Bottom', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['solid_button']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['sku']['value'] && $line_item['sku']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'SKU', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['sku']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['rush_my_order']['value'] && $line_item['rush_my_order']['key'] ) : ?>
									<li><strong><?php esc_html_e( 'Rush Manufacturing', 'hoodslyhub' ); ?></strong>
										<span>: <?php echo( $line_item['rush_my_order']['value'] ); ?> </span>
									</li>
								</ul>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
					<div class="total-price"><?php esc_html_e( 'Total: $', 'hoodslyhub' ); ?><?php echo $line_items['order_total']; ?></div>
				</div>

				<?php
			endwhile;
			wp_reset_postdata();
		endif;

		die();

		/* wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Ventilation Order Picked.',
			)
		); */
	}
}

add_action( 'wp_ajax_print_order_details', 'print_order_details' );

/**
 * Download Bulk BOL file
 *
 * @return void
 * @throws exception
 */
function bulk_download_bol() {
	include_once __DIR__ . '/PDFMerger/PDFMerger.php';
	$upload_dir   = wp_upload_dir();
	$shipping_dir = $upload_dir['basedir'] . '/shipping_label';
	$pdf          = new PDFMerger\PDFMerger();
	function download_remote_file( $file_url, $save_to ) {
		$content = file_get_contents( $file_url );
		$data    = file_put_contents( $save_to, $content );
	}
	foreach ( $_POST['bol_id_array'] as $key => $value ) {
		if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
			download_remote_file('http://hoodslyhub.test/wp-content/uploads/bol/'.$value.'.pdf' , ''.$upload_dir['basedir'].'/'.$value.'.pdf');
		} else {
			download_remote_file('https://staging.hoodslyhub.com/wp-content/uploads/bol/'.$value.'.pdf' , ''.$upload_dir['basedir'].'/'.$value.'.pdf');
		}
		$pdf->addPDF( '' . $upload_dir['basedir'] . '/' . $value . '.pdf' );
	}

	$pdf->merge( 'file', '' . $upload_dir['basedir'] . '/bulk_bols.pdf' ); // generate the file
	//$pdf->merge('browser', ''.$upload_dir['basedir'].'/bulk_bols.pdf'); // force download

	$bulk_link = home_url() . '/wp-content/uploads/bulk_bols.pdf';
	wp_send_json(
		array(
			'success'   => true,
			'msg'       => 'Updated Successfully',
			'bulk_link' => $bulk_link,
		)
	);

}

add_action( 'wp_ajax_bulk_download_bol', 'bulk_download_bol' );

/**
 * Print Work Order BULK
 *
 * @return void
 */
function print_work_order() {
	$permission = check_ajax_referer( 'print_details', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Nonce error',
			)
		);
		wp_die();
	} else {
		$orderid = $_REQUEST['orderid'];

		$args  = array(
			'post_type' => 'wilkes_order',
			's'         => $orderid,
		);
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) :
				$query->the_post();
				?>
				<div class="dashboard-page__order-detail-body dashboard-page__order-detail-body-2">
					<?php
					$line_items = get_post_meta( get_the_ID(), 'line_items', true );
					foreach ( $line_items['line_items'] as $line_item ) :
						$product_name_color = $line_item['product_name'];
						$product_name_split = explode( '-', $product_name_color );
						$product_name       = $product_name_split[0];
						?>
						<div class="product-info" style="margin: 100px;padding: 0; width: auto;">
							<div class="text">
								<ul class="list-style">
									<li>
										<strong>Product Name</strong>
										<span>: <?php echo esc_html( $product_name ); ?></span>
									</li>

									<?php if ( 'quick-shipping' === $line_item['product_cat'] ) : ?>
										<li>
											<strong>Product ID</strong>
											<span>: #<?php echo intval( $line_item['product_id'] ); ?></span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['color']['value'] && $line_item['color']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Color', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo esc_html( $line_item['color']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['size']['value'] && $line_item['size']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Size', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['size']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['trim_options']['value'] && $line_item['trim_options']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Trim Options', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['trim_options']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['remove_trim']['value'] && $line_item['remove_trim']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'How Would You Like Your Trim?', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['remove_trim']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['crown_molding']['value'] && $line_item['crown_molding']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Crown Molding', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['crown_molding']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['increase_depth']['value'] && $line_item['increase_depth']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Increase Depth', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['increase_depth']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['reduce_height']['value'] && $line_item['reduce_height']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Reduce Height', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['reduce_height']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['extend_chimney']['value'] && $line_item['extend_chimney']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Extend Your Chimney', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['extend_chimney']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['solid_button']['value'] && $line_item['solid_button']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'Add A Solid Bottom', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['solid_button']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['sku']['value'] && $line_item['sku']['key'] ) : ?>
										<li><strong><?php esc_html_e( 'SKU', 'hoodslyhub' ); ?></strong>
											<span>: <?php echo( $line_item['sku']['value'] ); ?> </span>
										</li>
									<?php endif; ?>
									<?php if ( 'empty' !== $line_item['rush_my_order']['value'] && $line_item['rush_my_order']['key'] ) : ?>
									<li><strong><?php esc_html_e( 'Rush Manufacturing', 'hoodslyhub' ); ?></strong>
										<span>: <?php echo( $line_item['rush_my_order']['value'] ); ?> </span>
									</li>
								</ul>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
					<div class="total-price"><?php esc_html_e( 'Total: $', 'hoodslyhub' ); ?><?php echo $line_items['order_total']; ?></div>
				</div>

				<?php
			endwhile;
			wp_reset_postdata();
		endif;

		die();

		/* wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Ventilation Order Picked.',
			)
		); */
	}
}

add_action( 'wp_ajax_print_work_order', 'print_ordeprint_work_orderr_details' );