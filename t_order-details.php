<?php
/*
Template Name: Orders Details
*/
$order_id     = null;
$post_id      = null;
$edit         = null;
$shippingEdit = null;
if ( isset( $_GET['order_id'] ) && $_GET['order_id'] ) {
	$order_id = esc_html( $_GET['order_id'] );
	$post_id  = intval( $_GET['post_id'] );
}
if ( isset( $_GET['view'] ) && $_GET['view'] ) {
	$edit = $_GET['view'];
}

if ( isset( $_GET['shipping'] ) && $_GET['shipping'] ) {
	$shippingEdit = $_GET['shipping'];
}
$order_link = get_template_link( 't_order.php' );
if ( $order_link && $order_id == null ) {
	header( 'HTTP/1.1 301 Moved Permanently' );
	header( 'Location: ' . $order_link );
	exit();
}
get_header();
?>
	<div class="dashboard-page">
		<div class="container-fluid">
			<div class="row lr-10">
				<div class="col-xl-8 last-none">

					<div class="row lr-10">
						<div class="col-xl-12 col-lg-12">
							<div class="dashboard-page__order-detail">
								<div class="dashboard-page__order-detail-header">
									<div class="navbar-header">
										<?php if ( $edit === 'edit' ) : ?>
											<h6 class="title">Edit Order #<?php echo $order_id; ?> Details</h6>
										<?php else : ?>
											<h6 class="title">Order #<?php echo $order_id; ?> Details</h6>
										<?php endif; ?>
									</div>

									<div class="navbar-collapse">
										<ul class="navbar-nav ml-auto">
											<li><a href="#" class="icon-setting-sliders"></a></li>
										</ul>
									</div>
								</div>
								<?php if ( $edit === 'edit' ) : ?>
									<div class="dashboard-page__order-detail-body dashboard-page__order-detail-body-2">
										<form method="post" action="">
											<?php
											$line_items = get_post_meta( $post_id, 'line_items', true );

											foreach ( $line_items['line_items'] as $line_item ) :
												?>
												<div class="product-info">
													<figure class="media">
														<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( $line_item['product_img_url'] ), esc_html( $line_item['product_name'] ) ); ?>
													</figure>
													<div class="text">
														<ul class="list-style mb-3 edit-product">
															<?php $meta_data_arr = $line_item['order_meta']; ?>
															<?php
															if ( is_array( $meta_data_arr ) ) {
																foreach ( $meta_data_arr as $value ) {
																	$meta_value = str_replace(
																		array( '<p>', '</p>' ),
																		array(
																			'',
																			'',
																		),
																		html_entity_decode( $value['display_value'] )
																	);
																	?>
																	<li>
																		<strong><?php echo $value['display_key']; ?></strong>
																		<span>:
														<select name="product-type" class="order-select d-none" id="product-type">
															<option value="<?php echo $value['key']; ?>"><?php echo $meta_value; ?></option>

														</select>
													</span>
																	</li>
																	<?php
																}
															}
															?>

														</ul>
													</div>
												</div>
											<?php endforeach; ?>
											<div class="total-price">Total: $<?php echo $line_items['order_total']; ?></div>
											<div style="display: flex;justify-content: end;margin-top: 10px;">
												<button class="btn btn-bluedark" style="background: #AA4098; color: #fff">Update</button>
											</div>
										</form>
									</div>
								<?php else : ?>
									<div class="dashboard-page__order-detail-body dashboard-page__order-detail-body-2">
										<?php
										$line_items_arr    = get_post_meta( $post_id, 'line_items', true );
										$line_items        = isset( $line_items_arr ) && is_array( $line_items_arr ) ? $line_items_arr : array();
										$damage_item       = get_post_meta( $post_id, 'damage_item', true );
										$hood_replace      = get_post_meta( $post_id, 'hood_replace', true );
										$f_shelf_replace   = get_post_meta( $post_id, 'f_shelf_replace', true );
										$hall_tree_replace = get_post_meta( $post_id, 'hall_tree_replace', true );
										$no_replace        = get_post_meta( $post_id, 'no_replace', true );
										$miscellaneous     = get_post_meta( $post_id, 'miscellaneous', true );
										$origin            = get_post_meta( $post_id, 'origin', true );
										foreach ( $line_items['line_items'] as $line_item ) :
											$product_name_color = $line_item['product_name'];
											$product_name_split = explode( '-', $product_name_color );

											if ( 'diamondhoods.com' === $origin ) {
												$product_name = $line_item['product_name'];
											} else {
												$product_name = $product_name_split[0];
											}
											if ( ! empty( $line_item['item_sku'] ) ) {
												$item_sku = $line_item['item_sku'];
											}
											?>
											<div class="product-info">
												<figure class="media">
													<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( $line_item['product_img_url'] ), esc_html( $line_item['product_name'] ) ); ?>
												</figure>
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
																<span>: <?php echo esc_html( $line_item['size']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( isset( $line_item['style'] ) ? isset( $line_item['style'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['style']['value'] && $line_item['style']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Style', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['style']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['float_width'] ) ? isset( $line_item['float_width'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['float_width']['value'] && $line_item['float_width']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Float Width', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['float_width']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['float_depth'] ) ? isset( $line_item['float_depth'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['float_depth']['value'] && $line_item['float_depth']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Float Depth', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['float_depth']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['float_thick'] ) ? isset( $line_item['float_thick'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['float_thick']['value'] && $line_item['float_thick']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Float Thick', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['float_thick']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['qs_hood_size'] ) ? isset( $line_item['qs_hood_size'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['qs_hood_size']['value'] && $line_item['qs_hood_size']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Hood Size', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['qs_hood_size']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['qs_strapping'] ) ? isset( $line_item['qs_strapping'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['qs_strapping']['value'] && $line_item['qs_strapping']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Strapping', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['qs_strapping']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['qs_vent'] ) ? isset( $line_item['qs_vent'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['qs_vent']['value'] && $line_item['qs_vent']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'vent', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['qs_vent']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['custom_color_match'] ) ? isset( $line_item['custom_color_match'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['custom_color_match']['value'] && $line_item['custom_color_match']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Custom Color Match', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['custom_color_match']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['finish'] ) ? isset( $line_item['finish'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['finish']['value'] && $line_item['finish']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Finish', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['finish']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['ventilation'] ) ? isset( $line_item['ventilation'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['ventilation']['value'] && $line_item['ventilation']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Ventilation', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['ventilation']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['recirculating_filter'] ) ? isset( $line_item['recirculating_filter'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['recirculating_filter']['value'] && $line_item['recirculating_filter']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Recirculating Filter', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['recirculating_filter']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['recirculating_vent'] ) ? isset( $line_item['recirculating_vent'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['recirculating_vent']['value'] && $line_item['recirculating_vent']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Recirculating Vent', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['recirculating_vent']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['ventilation_size'] ) ? isset( $line_item['ventilation_size'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['ventilation_size']['value'] && $line_item['ventilation_size']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Ventilation Size', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['ventilation_size']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['get_loose'] ) ? isset( $line_item['get_loose'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['get_loose']['value'] && $line_item['get_loose']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Get Loose', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['get_loose']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['extend_chimney'] ) ? isset( $line_item['extend_chimney'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['extend_chimney']['value'] && $line_item['extend_chimney']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Extend Chimney', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['extend_chimney']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['trim_options'] ) ? isset( $line_item['trim_options'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['trim_options']['value'] && $line_item['trim_options']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Trim Options', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['trim_options']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['remove_trim'] ) ? isset( $line_item['remove_trim'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['remove_trim']['value'] && $line_item['remove_trim']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'How Would You Like Your Trim?', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['remove_trim']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['crown_molding'] ) ? isset( $line_item['crown_molding'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['crown_molding']['value'] && $line_item['crown_molding']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Crown Molding', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['crown_molding']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['increase_depth'] ) ? isset( $line_item['increase_depth'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['increase_depth']['value'] && $line_item['increase_depth']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Increase Depth', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['increase_depth']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['reduce_height'] ) ? isset( $line_item['reduce_height'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['reduce_height']['value'] && $line_item['reduce_height']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Reduce Height', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['reduce_height']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['extend_chimney'] ) ? isset( $line_item['extend_chimney'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['extend_chimney']['value'] && $line_item['extend_chimney']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Extend Your Chimney', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['extend_chimney']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['solid_button'] ) ? isset( $line_item['solid_button'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['solid_button']['value'] && $line_item['solid_button']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Add A Solid Bottom', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['solid_button']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['sku'] ) ? isset( $line_item['sku'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['sku']['value'] && $line_item['sku']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'SKU', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['sku']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset( $line_item['rush_my_order'] ) ? isset( $line_item['rush_my_order'] ) : array() ) : ?>
															<?php if ( 'empty' !== $line_item['rush_my_order']['value'] && $line_item['rush_my_order']['key'] ) : ?>
																<li><strong><?php esc_html_e( 'Rush Manufacturing', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['rush_my_order']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( 'Wood Hoods' === $damage_item || 'Island Wood Hoods' === $damage_item || 'Quick Shipping' === $damage_item ) : ?>
															<li><strong><?php esc_html_e( 'What needs to be replaced', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( ucfirst( $hood_replace ) ); ?> </span>
															</li>
														<?php elseif ( 'Floating Shelves' === $damage_item ) : ?>
															<li><strong><?php esc_html_e( 'What needs to be replaced', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( ucfirst( $f_shelf_replace ) ); ?> </span>
															</li>
														<?php elseif ( 'Hall Trees' === $damage_item ) : ?>
															<li><strong><?php esc_html_e( 'What needs to be replaced', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( ucfirst( $hall_tree_replace ) ); ?> </span>
															</li>
														<?php else : ?>
															<?php if ( $no_replace ) : ?>
																<li><strong><?php esc_html_e( 'What needs to be replaced', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( ucfirst( $no_replace ) ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( 'miscellaneous' === $hood_replace || 'miscellaneous' === $f_shelf_replace || 'miscellaneous' === $hall_tree_replace ) : ?>
															<li><strong><?php esc_html_e( 'Miscellaneous', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( $miscellaneous ); ?> </span>
															</li>
														<?php endif; ?>
														<?php
														if ( isset( $item_sku ) && ! empty( $item_sku ) ) {
															?>
															<li>
																<strong>SKU</strong>
																<span>: <?php echo esc_html( $item_sku ); ?></span>
															</li>
															<?php
														}
														?>
													</ul>
												</div>
											</div>
										<?php endforeach; ?>
										<div class="total-price">Total: $<?php echo esc_html( $line_items['order_total'] ); ?></div>
									</div>
								<?php endif; ?>
							</div>
						</div>
						<?php
						$billing    = get_post_meta( $post_id, 'billing', true );
						$first_name = ( isset( $billing['first_name'] ) && ! empty( $billing['first_name'] ) ) ? $billing['first_name'] : '';
						$last_name  = ( isset( $billing['last_name'] ) && ! empty( $billing['last_name'] ) ) ? $billing['last_name'] : '';
						$phone      = ( isset( $billing['phone'] ) && ! empty( $billing['phone'] ) ) ? $billing['phone'] : '';
						$email      = ( isset( $billing['email'] ) && ! empty( $billing['email'] ) ) ? $billing['email'] : '';
						$address_1  = ( isset( $billing['address_1'] ) && ! empty( $billing['address_1'] ) ) ? $billing['address_1'] : '';
						$address_2  = ( isset( $billing['address_2'] ) && ! empty( $billing['address_2'] ) ) ? $billing['address_2'] : '';
						$city       = ( isset( $billing['city'] ) && ! empty( $billing['city'] ) ) ? $billing['city'] : '';
						$state      = ( isset( $billing['first_name'] ) && ! empty( $billing['first_name'] ) ) ? $billing['first_name'] : '';
						$state      = ( isset( $billing['state'] ) && ! empty( $billing['state'] ) ) ? $billing['state'] : '';
						$postcode   = ( isset( $billing['postcode'] ) && ! empty( $billing['postcode'] ) ) ? $billing['postcode'] : '';
						?>
						<div class="col-xl-6 col-lg-12">
							<div class="dashboard-page__shipping-address">
								<div class="dashboard-page__shipping-address-header">
									<div class="navbar-header">
										<h6 class="title"><?php esc_attr_e( 'Shipping Address', 'hoodslyhub' ); ?></h6>
									</div>
								</div>
								<?php
								if ( isset( $_POST['save_bol_shipping'] ) && ! empty( $_POST ) ) {

									$name_arr    = explode( ' ', $_POST['shipping-name'] );
									$firstname   = isset( $name_arr[0] ) ? $name_arr[0] : '';
									$lastname    = isset( $name_arr[1] ) ? $name_arr[1] : '';
									$address_arr = array(
										'first_name' => $firstname,
										'last_name'  => $lastname,
										'phone'      => $_POST['shipping-phone'],
										'email'      => $_POST['shipping-email'],
										'address_1'  => $_POST['shipping-address-one'],
										'address_2'  => $_POST['shipping-address-two'],
										'city'       => $_POST['shipping-city'],
										'state'      => $_POST['shipping-state'],
										'postcode'   => $_POST['shipping-postcode'],
										'country'    => 'US',
									);
									update_post_meta( $post_id, 'shipping', $address_arr );

									$Hoodslyhub_Rl_Carrriers_Lading = new Hoodslyhub_Rl_Carrriers_Lading();
									$Hoodslyhub_Rl_Carrriers_Lading->create_bol_dir();
									$Hoodslyhub_Rl_Carrriers_Lading->initiating_rl_lading( $post_id );
								}
								$shipping_add = get_post_meta( $post_id, 'shipping', true );
								$shipping     = ( isset( $shipping_add ) && is_array( $shipping_add ) ) ? $shipping_add : array();
								if ( $shipping ) :
									$s_first_name = ( isset( $shipping['first_name'] ) && ! empty( $shipping['first_name'] ) ) ? $shipping['first_name'] : '';
									$s_last_name  = ( isset( $shipping['last_name'] ) && ! empty( $shipping['last_name'] ) ) ? $shipping['last_name'] : '';
									$s_address_1  = ( isset( $shipping['address_1'] ) && ! empty( $shipping['address_1'] ) ) ? $shipping['address_1'] : '';
									$s_address_2  = ( isset( $shipping['address_2'] ) && ! empty( $shipping['address_2'] ) ) ? $shipping['address_2'] : '';
									$s_city       = ( isset( $shipping['city'] ) && ! empty( $shipping['city'] ) ) ? $shipping['city'] : '';
									$s_state      = ( isset( $shipping['state'] ) && ! empty( $shipping['state'] ) ) ? $shipping['state'] : '';
									$s_postcode   = ( isset( $shipping['postcode'] ) && ! empty( $shipping['postcode'] ) ) ? $shipping['postcode'] : '';
									?>
									<?php if ( $shippingEdit === 'shipping_edit' ) { ?>
									<form action="" method="POST" name="save_ashipping_bol" id="save_ashipping_bol">
										<div class="dashboard-page__shipping-address-body">
											<input class="form-control" type="text" name="shipping-name" id="shipping-name" placeholder="Name"
												   value="<?php echo $s_first_name . ' ' . $s_last_name; ?>">
											<input class="form-control" type="number" name="shipping-phone" id="shipping-phone" placeholder="Phone"
												   value="<?php echo esc_html( $phone ); ?>">
											<input class="form-control" type="text" name="shipping-email" id="shipping-email" placeholder="Email"
												   value="<?php echo esc_html( $email ); ?>">
											<input class="form-control" type="text" name="shipping-address-one" id="shipping-address-one"
												   placeholder="Address Line One" value="<?php echo esc_html( $s_address_1 ); ?>">
											<input class="form-control" type="text" name="shipping-address-two" id="shipping-address-two"
												   placeholder="Address Line Two" value="<?php echo esc_html( $s_address_2 ); ?>">
											<input class="form-control" type="text" name="shipping-city" id="shipping-city"
												   placeholder="Shipping City" value="<?php echo esc_html( $s_city ); ?>">
											<input class="form-control" type="text" name="shipping-state" id="shipping-state"
												   placeholder="Shipping State" value="<?php echo esc_html( $s_state ); ?>">
											<input class="form-control" type="text" name="shipping-postcode" id="shipping-postcode"
												   placeholder="Shipping Postcode" value="<?php echo esc_html( $s_postcode ); ?>">
											<button class="btn btn-submit" name="save_bol_shipping">
											<?php
											esc_html_e( 'Save Change', 'hoodslyhub' );
											?>
												</button>
										</div>
									</form>
								<?php } else { ?>
									<div class="dashboard-page__shipping-address-body">
										<div class="name"><?php echo $s_first_name . ' ' . $s_last_name; ?></div>
										<div class="phone"><?php echo esc_html( $phone ); ?></div>
										<div class="email"><a
													href="mailto:<?php echo esc_html( $email ); ?>"><?php echo esc_html( $email ); ?></a>
										</div>
										<div class="address"><?php echo esc_html( $s_address_1 ); ?> <br><?php echo esc_html( $s_city ); ?>
											<br> <?php echo esc_html( $s_state ); ?> <br><?php echo esc_html( $s_postcode ); ?>
										</div>
									</div>
								<?php } ?>
								<?php else : ?>
									<div class="dashboard-page__shipping-address-body">
										<div class="name"><?php echo $first_name . ' ' . $last_name; ?></div>
										<div class="phone"><?php echo esc_html( $phone ); ?></div>
										<div class="email"><a
													href="mailto:<?php echo esc_html( $email ); ?>"><?php echo esc_html( $email ); ?></a>
										</div>
										<div class="address"><?php echo esc_html( $address_1 ); ?> <br><?php echo esc_html( $city ); ?>
											<br> <?php echo esc_html( $state ); ?> <br><?php echo esc_html( $postcode ); ?>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
						<div class="col-xl-6 col-lg-12">
							<div class="dashboard-page__shipping-address">
								<div class="dashboard-page__shipping-address-header">
									<div class="navbar-header">
										<h6 class="title"><?php esc_attr_e( 'Billing Address', 'hoodslyhub' ); ?></h6>
									</div>
								</div>

								<div class="dashboard-page__shipping-address-body">
									<div class="name"><?php echo $first_name . ' ' . $last_name; ?></div>
									<div class="phone"><?php echo esc_html( $phone ); ?></div>
									<div class="email"><a href="mailto:<?php echo esc_html( $email ); ?>"><?php echo esc_html( $email ); ?></a>
									</div>
									<div class="address"><?php echo esc_html( $address_1 ); ?> <br><?php echo esc_html( $city ); ?>
										<br> <?php echo esc_html( $state ); ?> <br><?php echo esc_html( $postcode ); ?>
									</div>
								</div>
							</div>
						</div>

						<?php get_template_part( 'template-parts/order', 'communication' ); ?>
					</div>
				</div>
				<div class="col-xl-4">
					<div class="dashboard-page__order">
						<div class="dashboard-page__order-header">
							<div class="navbar-header">
								<h6 class="title">Actions</h6>
							</div>

							<div class="navbar-collapse">
								<ul class="navbar-nav ml-auto">
									<li><a href="#" class="icon-setting-sliders"></a></li>
								</ul>
							</div>
						</div>

						<div class="dashboard-page__order-body">
							<ul class="action-list list-unstyled">
								<?php
								global $wp;
								$origin            = get_post_meta( $post_id, 'origin', true );
								$order_id          = get_post_meta( $post_id, 'order_id', true );
								$billing           = get_post_meta( $post_id, 'billing', true );
								$email             = ( isset( $billing['email'] ) && ! empty( $billing['email'] ) ) ? $billing['email'] : '';
								$tracking_number   = get_post_meta( $post_id, '_aftership_tracking_number', true );
								$tracking_provider = get_post_meta( $post_id, '_aftership_tracking_provider_name', true );
								//$order_edit_link    = get_template_link( 't_order-edit.php' );
								$current_url = home_url( $wp->request );
								$edit_url    = add_query_arg(
									array(
										'view'     => 'edit',
										'post_id'  => $post_id,
										'order_id' => $order_id,
									),
									$current_url
								);
								//$shipping_edit_link = get_template_link( 't_shipping-edit.php' );
								$shipping_edit_url      = add_query_arg(
									array(
										'shipping' => 'shipping_edit',
										'post_id'  => $post_id,
										'order_id' => $order_id,
									),
									$current_url
								);
								$product_cat            = get_post_meta( get_the_ID(), 'product_cat', true );
								$is_tradewinds_selected = get_post_meta( $post_id, 'is_tradewinds_selected', true );
								if ( $is_tradewinds_selected !== 'yes' && $product_cat !== 'tradewinds-inserts' ) {
									?>
									<li>
										<a href="" data-orderid="<?php echo $order_id; ?>" data-postid="<?php echo $post_id; ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'request_ventilation_nonce' ) ); ?>" class="btn-action request_vent"><?php esc_html_e( 'Request Ventilation', 'hoodslyhub' ); ?></a>
									</li>
							<?php } else { ?>
								<li>
									<button type="button" class="btn btn-info" disabled="disabled">Already in ventilation list</button>
								</li>
							<?php } ?>
							</ul>
						</div>

					</div>


					<div class="dashboard-page__order">
						<div class="dashboard-page__order-header">
							<div class="navbar-header">
								<h6 class="title"><?php echo esc_html__( 'Order History', 'hoodslyhub' ); ?></h6>
							</div>

							<div class="navbar-collapse">
								<ul class="navbar-nav ml-auto">
									<li><a href="#" class="icon-setting-sliders"></a></li>
								</ul>
							</div>
						</div>
						<div class="dashboard-page__order-body">

							<div id="history-simplebar">
								<ul class="order-history-list list-unstyled">
									<?php
									$order_summerys = get_post_meta( $post_id, 'order_summery', true );
									?>
									<?php
									if ( ! empty( $order_summerys ) ) {
										foreach ( $order_summerys as $order_summery ) :
											?>

										<li>
											<span class="text"><?php echo esc_html( $order_summery['summery'] ); ?></span>
											<?php $historyDate = date( 'F jS g:i a', strtotime( $order_summery['date'] ) ); ?>
											<span class="date"><?php echo $historyDate; ?></span>
										</li>
											<?php
									endforeach;
									}
									?>
								</ul>

							</div>
						</div>
					</div>
					
					<?php
					$order_status            = get_post_meta( $post_id, 'order_status', true );
					$order_date              = get_post_meta( $post_id, 'order_date', true );
					$bill_of_landing_id      = intval( get_post_meta( $post_id, 'bill_of_landing_id', true ) );
					$orderDate               = date( 'F jS g:i a', strtotime( $order_date ) );
					$origin                  = get_post_meta( $post_id, 'origin', true );
					$domain_parts            = explode( '.', $origin );
					$es_shipping_date        = get_post_meta( $post_id, 'estimated_shipping_date', true );
					$shipping_date           = date( 'F jS', strtotime( $es_shipping_date ) );
					$estimated_shipping_date = $shipping_date ?? '';
					$bol_link                = 'http://staging.wrhhub.com' . '/wp-content/uploads/bol/' . $bill_of_landing_id . '.pdf';
					$shipping_file_link      = 'http://staging.wrhhub.com' . '/wp-content/uploads/bol/shipping_label_' . $bill_of_landing_id . '.pdf';
					?>
					<div class="dashboard-page__order">
						<div class="dashboard-page__order-header">
							<div class="navbar-header">
								<h6 class="title"><?php esc_attr_e( 'Order Summary', 'hoodslyhub' ); ?></h6>
							</div>

							<div class="navbar-collapse">
								<ul class="navbar-nav ml-auto">
									<li class="btn-assemble"><a href="#"><?php echo esc_html( ucfirst( $order_status ) ); ?></a></li>
									<li><a href="#" class="icon-setting-sliders"></a></li>
								</ul>
							</div>
						</div>
						<div class="dashboard-page__order-body">
							<div class="order-summary">
								<?php if ( 'wrh' == get_post_type( $post_id ) ) : ?>
									<div class="order-summary__shop">
										<figure class="media">
											<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( get_theme_file_uri( 'assets/images/ryan.jpg' ) ), get_bloginfo( 'name' ) ); ?>
										</figure>

										<div class="text"><?php esc_html_e( 'Shop: ', 'hoodslyhub' ); ?><?php echo esc_html__( 'WRH', 'hoodslyhub' ); ?></div>
									</div>
								<?php elseif ( 'wilkes' == get_post_type( $post_id ) ) : ?>
									<div class="order-summary__shop">
										<figure class="media">
											<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( get_theme_file_uri( 'assets/images/ryan.jpg' ) ), get_bloginfo( 'name' ) ); ?>
										</figure>

										<div class="text"><?php esc_html_e( 'Shop: ', 'hoodslyhub' ); ?><?php echo esc_html__( 'Wilkes', 'hoodslyhub' ); ?></div>
									</div>
								<?php else : ?>
									<div class="order-summary__shop">
										<figure class="media">
											<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( get_theme_file_uri( 'assets/images/ryan.jpg' ) ), get_bloginfo( 'name' ) ); ?>
										</figure>

										<div class="text"><?php esc_html_e( 'Shop: ', 'hoodslyhub' ); ?><?php echo esc_html__( 'Not Assigned Yet', 'hoodslyhub' ); ?></div>
									</div>
								<?php endif; ?>
								<ul class="order-summary__list">
									<li><i class="icon-line-chart"></i>Order Created: <?php echo esc_html( $orderDate ); ?></li>
									<li><i class="icon-line-chart"></i>Order Source: <?php echo esc_html( ucfirst( $domain_parts[0] ) ); ?>
									</li>
									<li><i class="icon-line-chart"></i>Estimated Shipping
										Date: <?php echo esc_html( $estimated_shipping_date ); ?></li>
									<li><i class="icon-line-chart"></i>BOL: <a style="color: #000; padding-left: 5px"
																			   href="<?php echo esc_url( $bol_link ); ?>"
																			   target="_blank"><?php echo esc_html__( 'View File', 'hoodslyhub' ); ?></a>
									</li>
									<li><i class="icon-line-chart"></i>Shipping Label: <a style="color: #000; padding-left: 5px"
																						  href="<?php echo esc_url( $shipping_file_link ); ?>"
																						  target="_blank"><?php echo esc_html__( 'View File', 'hoodslyhub' ); ?></a>
									</li>
									<li><i class="icon-line-chart"></i>Packing List: {packing_file}</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
get_footer();
