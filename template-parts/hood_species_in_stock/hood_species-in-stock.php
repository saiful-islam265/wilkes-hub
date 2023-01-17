<?php 

 /**
 * Sanitize an array of data
 * @param  array   $NonSanitzedData
 * @return mixed
 */
function sanitizeData(array $NonSanitzedData) {
	$sanitizedData = null;

	$sanitizedData = array_map(function ($data) {
		if (gettype($data) == 'array') {
			return $this->sanitizeData($data);
		} else {
			return sanitize_text_field($data);
		}
	}, $NonSanitzedData);

	return $sanitizedData;
}

if ( isset ( $_POST['wood_species_submit'] ) && $_POST['wood_species_submit'] === 'yes' ) {
	
	$arg  = $_POST['wood_spacies_in_stock'] ? $_POST['wood_spacies_in_stock'] : [];
	$data = sanitizeData($arg);

	$api_endpoint  = get_option( 'hoodslyhub_api_settings' );
	$wood_species_endpoint = '';
	foreach ( $api_endpoint['hub_order_status_endpoint']['feed'] as $key => $value ) {
		if ( 'wood_species_instock_product_request' === $value['end_point_type'] ) {
			$wood_species_endpoint = $value['end_point_url'];
		}
	}

	$api_secret    = get_option( 'hoodslyhub_api_credentials' );
	$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
	
	$response      = wp_remote_post(
		$wood_species_endpoint,
		array(
			'body'    => wp_json_encode($data),
			'headers' => array(
				'content-type'  => 'application/json',
				'Api-Signature' => $api_signature,
			),
		)
	);

	if ( $response['response']['code'] === 200 ) {
		update_option('wood_spacies_in_stock_checked', $data);
	}
	
}
?>
<div class="col-xl-12">
	<form action="" method="post">
	<div class="dashboard-page__order">
		<div class="dashboard-page__order-header">
			<div class="navbar-header" style="display: flex;">
				<h6 class="title">Wood Species In Stock</h6>
				<div class="bulk_action_btn_section">
					<button type="submit" value="yes" name="wood_species_submit" class="btn btn-success">Save</button>
				</div>
			</div>

			<div class="navbar-collapse">
				<ul class="navbar-nav ml-auto">
					
					<li><a href="#" class="icon-setting-sliders"></a></li>
				</ul>
			</div>
		</div>
		<div class="dashboard-page__order-body" id="floating-shelves-order-list">
			<table class="table table-order">
				<thead>
				<tr>
					<th scope="col">Wood Species Products</th>
					<th scope="col"></th>
				</tr>
				</thead>
				<tbody>
					<?php 
					$Hood_Spacises = get_option('wilkes_Hood_Species_Options') ? get_option('wilkes_Hood_Species_Options') : [];
					$in_stock_checked = get_option('wood_spacies_in_stock_checked') ? get_option('wood_spacies_in_stock_checked') : [];
					
					if ( $Hood_Spacises ) { 
						foreach ( $Hood_Spacises as $Hood_Spacis ) {
						$checked = in_array($Hood_Spacis['key'], $in_stock_checked) ? 'checked' : '';
					?>
						<tr style="background-color: #4747471a;">
							<td>
								<input type="checkbox" name="wood_spacies_in_stock[]" id="<?php echo $Hood_Spacis['key']; ?>" value="<?php echo $Hood_Spacis['key']; ?> " <?php echo esc_html($checked); ?>/>
								<label for="<?php echo $Hood_Spacis['key']; ?>"><?php echo $Hood_Spacis['value']; ?></label>
							</td>
							<td></td>
						</tr>
						
						<?php
					}
					
				} else {
					?>
					<tr style="background-color: #4747471a;">
						<td colspan="100%" class="text-left"><?php esc_html_e( 'There are no Products', 'hoodslyhub' ); ?></td>
					</tr>
					<?php
				} 
				?>
				</tbody>
			</table>
			
		</div>
	</div>
	</form>
</div>
