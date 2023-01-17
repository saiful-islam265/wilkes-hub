<div class="col-xl-12">
	<div class="dashboard-page__order">
		<div class="dashboard-page__order-header">
			<div class="navbar-header" style="display: flex;">
				<h6 class="title"><?php esc_html_e( 'Active Orders', 'wilkeshub' ); ?></h6>
				<div class="bulk_edit">
					<button type="submit" class="btn btn-warning"><?php esc_html_e( 'Bulk Edit', 'wilkeshub' ); ?></button>
				</div>
				<div class="order_change_bulk">
					<select class="form-control select_status" name="" id="">
						<option><?php esc_html_e( 'Order Status', 'wilkeshub' ); ?></option>
						<option value="Engineer/Routing"><?php esc_html_e( 'Engineer/Routing', 'wilkeshub' ); ?></option>
						<option value="Build"><?php esc_html_e( 'Build', 'wilkeshub' ); ?></option>
						<option value="Sanding/Add Features"><?php esc_html_e( 'Sanding/Add Features', 'wilkeshub' ); ?></option>
						<option value="Paint"><?php esc_html_e( 'Paint', 'wilkeshub' ); ?></option>
						<option value="Gather Loose Items/Packing"><?php esc_html_e( 'Gather Loose Items/Packing', 'wilkeshub' ); ?></option>
						<option value="This Weeks Shipments"><?php esc_html_e( 'This Weeks Shipments', 'wilkeshub' ); ?></option>
					</select>
				</div>
				<div class="bulk_action_btn_section">
					<button type="submit" class="btn btn-danger bulk_download_bol"><?php esc_html_e( 'Print BOL & Shipping Label', 'wilkeshub' ); ?></button>
				</div>
				<div class="bulk_action_btn_section">
					<button type="submit" class="btn btn-danger bulk_print_work_order"><?php esc_html_e( 'Print Work Order', 'wilkeshub' ); ?></button>
				</div>
			</div>

			<div class="navbar-collapse">
				<ul class="navbar-nav ml-auto">
					<li class="dropdown">
						<a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php esc_html_e( 'Create', 'wilkeshub' ); ?></a>

						<ul class="dropdown-menu">
							<li><a href="#"><?php esc_html_e( 'New Order', 'wilkeshub' ); ?></a></li>
							<li><a href="#"><?php esc_html_e( 'Create Invoice', 'wilkeshub' ); ?></a></li>
						</ul>
					</li>
					<li><a href="#" class="icon-setting-sliders"></a></li>
				</ul>
			</div>
		</div>
		<div class="dashboard-page__order-body" id="wilkes-order-list">
			<table class="table table-order">
				<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Order', 'wilkeshub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Customer Info', 'wilkeshub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Order Status', 'wilkeshub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Order Source', 'wilkeshub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Is Priority?', 'wilkeshub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Ship Method', 'wilkeshub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Bill Of Lading', 'wilkeshub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Shipping Label', 'wilkeshub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Items', 'wilkeshub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Actions', 'wilkeshub' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				$hub_paged              = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				$default_posts_per_page = get_option( 'posts_per_page' );
				$args                   = array(
					'post_type'      => 'wilkes_order',
					'posts_per_page' => $default_posts_per_page,
					'paged'          => $hub_paged,
					'orderby'        => 'ID',
					'order'          => 'DESC',
					'meta_query'     => array(
						array(
							'key'     => 'custom_color_match',
							'value'   => '0',
							'compare' => 'LIKE',
						),
						array(
							'key'     => 'completion_date',
							'value'   => 'none',
							'compare' => 'LIKE',
						),
						array(
							'key'     => 'order_status',
							'value'   => 'This Weeks Shipments',
							'compare' => 'NOT LIKE',
						),
					),
				);
				$all_orders             = new WP_Query( $args );
				if ( $all_orders->have_posts() ) {
					while ( $all_orders->have_posts() ) {
						$all_orders->the_post();
						$bill_of_landing_id          = intval( get_post_meta( get_the_ID(), 'bill_of_landing_id', true ) );
						$shipping_add                = get_post_meta( get_the_ID(), 'shipping', true );
						$shipping                    = ( isset( $shipping_add ) && is_array( $shipping_add ) ) ? $shipping_add : array();
						$first_name                  = ( isset( $shipping['first_name'] ) && ! empty( $shipping['first_name'] ) ) ? $shipping['first_name'] : '';
						$last_name                   = ( isset( $shipping['last_name'] ) && ! empty( $shipping['last_name'] ) ) ? $shipping['last_name'] : '';
						$address_1                   = ( isset( $shipping['address_1'] ) && ! empty( $shipping['address_1'] ) ) ? $shipping['address_1'] : '';
						$address_2                   = ( isset( $shipping['address_2'] ) && ! empty( $shipping['address_2'] ) ) ? $shipping['address_2'] : '';
						$city                   = ( isset( $shipping['city'] ) && ! empty( $shipping['city'] ) ) ? $shipping['city'] : '';
						$state                   = ( isset( $shipping['state'] ) && ! empty( $shipping['state'] ) ) ? $shipping['state'] : '';
						$postcode                   = ( isset( $shipping['postcode'] ) && ! empty( $shipping['postcode'] ) ) ? $shipping['postcode'] : '';
						$country                   = ( isset( $shipping['country'] ) && ! empty( $shipping['country'] ) ) ? $shipping['country'] : '';
						$order_status                = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
						$es_shipping_date            = get_post_meta( get_the_ID(), 'estimated_shipping_date', true );
						$shipping_date               = gmdate( 'F jS Y', strtotime( $es_shipping_date ) );
						$estimated_shipping_date     = $shipping_date ?? '';
						$origin                      = get_post_meta( get_the_ID(), 'origin', true );
						$shipping_lines_arr          = get_post_meta( get_the_ID(), 'shipping_lines', true );
						$shipping_lines              = isset( $shipping_lines_arr ) && is_array( $shipping_lines_arr ) ? $shipping_lines_arr : array();
						$shipping_lines_method_title = ( isset( $shipping_lines['method_title'] ) && ! empty( $shipping_lines['method_title'] ) ) ? $shipping_lines['method_title'] : '';
						$domain_parts                = explode( '.', $origin );
						$order_link                  = get_template_link( 't_order-details.php' );
						$order_id                    = get_post_meta( get_the_ID(), 'order_id', true );
						$line_items                  = get_post_meta( get_the_ID(), 'line_items', true );
						$is_priority                 = get_post_meta( get_the_ID(), 'rush_manufacturing', true );
						$damage_item                 = get_post_meta( get_the_ID(), 'damage_item', true );
						$hood_replace                = get_post_meta( get_the_ID(), 'hood_replace', true );
						$f_shelf_replace             = get_post_meta( get_the_ID(), 'f_shelf_replace', true );
						$hall_tree_replace           = get_post_meta( get_the_ID(), 'hall_tree_replace', true );
						$no_replace                  = get_post_meta( get_the_ID(), 'no_replace', true );
						$bol_link                    = get_post_meta( get_the_ID(), 'bol_pdf', true );
						$shipping_file_link          = get_post_meta( get_the_ID(), 'shipping_label', true );
						$damage_claim_id           	 = get_post_meta( get_the_ID(), 'damage_claim_id', true );
						$backgroundg_color           = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
						$bol_regenerated             = get_post_meta( get_the_ID(), 'bol_regenerated', true );
						$checked                     = ( 'yes' === $is_priority ) ? 'checked' : '';
						$disabled                    = '';

						$style_priority = '';
						
						if (!empty($damage_claim_id) ) {
							$priority_str = 'Replacement';
							$disabled    = '';
						} elseif('rush_my_order' == $is_priority) {
							$priority_str = 'Rushed';
							$disabled    = '';
						}else {
							$priority_str = 'Normal';
							$style_priority = 'text-decoration-line: line-through';
							$disabled    = 'disabled';
						}

						?>

						<tr style="background-color: #4747471a;">
							<td data-title="Order Id">
							<input type="checkbox" class="bulk_check" value="test" data-bol_id="<?php echo intval( $bill_of_landing_id ); ?>" data-orderid="<?php echo esc_html( $order_id ); ?>" data-postid="<?php echo get_the_ID(); ?>"
							data-orderurl="
								<?php
								echo esc_url(
									add_query_arg(
										array(
											'post_id'  => get_the_ID(),
											'order_id' => $order_id,
										),
										$order_link
									)
								);
								?>
								"/>
							<a href="
							<?php
							echo esc_url(
								add_query_arg(
									array(
										'post_id'  => get_the_ID(),
										'order_id' => $order_id,
									),
									$order_link
								)
							)
							?>
								"><?php the_title(); ?></a>
                            </td>
							<td data-title="Customer Info"><?php echo esc_html( $first_name ) . ' ' . esc_html( $last_name ); ?></td>
							<td data-title="Order Status">
								<button class="btn btn-violet" <?php echo esc_attr( $backgroundg_color ); ?>>
									<?php echo esc_html( $order_status ); ?>
								</button>
							</td>
							<td data-title="order source"><?php echo esc_html( $origin ); ?></td>

							<td data-title="Free Curbside Delivery"><button class="btn btn-danger <?php echo esc_attr( $disabled ); ?>" style="<?php echo esc_attr( $style_priority ); ?>"><?php echo $priority_str; ?></button></td>
							<td data-title="Order Source"><?php echo esc_html( $shipping_lines_method_title ); ?></td>
							<td class="files" data-title="Files" data-toggle="tooltip" data-placement="right">
								<button class="btn btn-violet"><a href="<?php echo esc_url( $bol_link ); ?>"><?php esc_html_e( 'View', 'wilkeshub' ); ?></a></button>
							</td>
							<td class="files" data-title="Files" data-toggle="tooltip" data-placement="right">
								<button class="btn btn-violet"><a href="<?php echo esc_url( $shipping_file_link ); ?>"><?php esc_html_e( 'View', 'wilkeshub' ); ?></a></button>
							</td>
							<td data-title="Order Notes">
								<button class="btn btn-border" type="button" data-toggle="collapse" data-target="#notes-<?php echo esc_html( $order_id ); ?>" aria-expanded="false" aria-controls="notes-<?php echo esc_html( $order_id ); ?>"><?php esc_html_e( 'Item Detail', 'wilkeshub' ); ?>
								</button>
							</td>
							<td data-title="Actions" class="action-dropdown dropdown">
								<div role="button" class="icon-dots" data-toggle="dropdown">
									<span></span><span></span><span></span></div>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="
									<?php
									echo esc_url(
										add_query_arg(
											array(
												'post_id'  => get_the_ID(),
												'order_id' => $order_id,
											),
											$order_link
										)
									)
									?>
										"><?php echo esc_html__( 'View', 'wilkeshub' ); ?></a></li>
										<li><a href="#" data-orderid="<?php echo get_the_ID(); ?>" class="wilkeshub-delete-order" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wilkeshub_delete_order_nonce' ) ); ?>"><?php esc_html_e( 'Delete', 'wilkeshub' ); ?></a>
										</li>
								</ul>
							</td>
						</tr>
						<tr class="notes-collapse">
							<td colspan="12">
								<div class="notes-collapse__body collapse" id="notes-<?php echo esc_html( $order_id ); ?>">
									<div class="row">
										<div class="col-xl-12 col-lg-12 d-flex">
											<div class="notes-collapse__body-item order_pre_section">
											<?php
											$curved_array = array(
												'Curved With No Strapping',
												'Curved With Strapping',
												'Curved With Brass Strapping',
												'Curved With Stainless Steel Strapping',
											);
											$taperd_array = array( 'Tapered Straight', 'Tapered Shiplap', 'Tapered With Strapping' );
											$sloped_array = array( 'Sloped No Strapping', 'Sloped Strapping' );
											$angled_array = array( 'Angled No Strapping', 'Angled With Strapping', 'Angled With Walnut Band' );

											if ( in_array( $line_items['line_items'][0]['product_name'], $curved_array, true ) ) {
												$hood_style = 'Charleston';
											} elseif ( in_array( $line_items['line_items'][0]['product_name'], $taperd_array, true ) ) {
												$hood_style = 'Manchester';
											} elseif ( 'Box With Trim' === $line_items['line_items'][0]['product_name'] ) {
												$hood_style = 'Belfast';
											} elseif ( in_array( $line_items['line_items'][0]['product_name'], $sloped_array, true ) ) {
												$hood_style = 'Venice';
											} elseif ( in_array( $line_items['line_items'][0]['product_name'], $angled_array, true ) ) {
												$hood_style = 'London';
											} else {
												$hood_style = '';
											}
											?>
											<div class="product_style_info">
												<div class="syle_finish_sec">
													<h4 class="style_head"><?php esc_html_e( 'Hood Style', 'wilkeshub' ); ?></h4>
													<p><?php esc_html_e( 'Wood Species: Maple', 'wilkeshub' ); ?></p>
													<p><?php esc_html_e( 'Hood Style: ', 'wilkeshub' ); ?><?php echo esc_html( $hood_style ); ?></p>
													<p><?php esc_html_e( 'Trim Style: ', 'wilkeshub' ); ?><?php echo 'Classic'; ?></p>
													<p><?php esc_html_e( 'Trim Installation: ', 'wilkeshub' ); ?><?php echo 'No Strapping'; ?></p>
													<?php
														$line_items = get_post_meta( get_the_ID(), 'line_items', true );
														//$trim = explode(' ', trim($line_items['line_items'] ))[0];
														$customer_note = get_post_meta( get_the_ID(), 'customer_note', true );
													$reduce_height = '';
													$crown_molding = '';
													foreach ( $line_items['line_items'] as $key => $s_item ) {
														$product_name   = explode( ' ', trim( $s_item['product_name'] ) )[0];
														$trim_options   = explode( ' ', trim( $s_item['trim_options']['value'] ) )[0];
														$size           = preg_match( '/([0-9]+)/', $s_item['size']['key'], $height_width );
														$increase_depth = preg_match( '/([0-9]+)\.([0-9]+)/', $s_item['increase_depth']['value'], $depth );
														$reduce_height  = preg_match( '~(?|([^"]*)"|\'([^\']*)\')~', $s_item['reduce_height']['value'], $reduce );
														$crown_molding  = explode( ' ', trim( $s_item['crown_molding']['value'] ) )[0];
														$extend_chimney = explode( ' ', trim( $s_item['extend_chimney']['value'] ) )[0];
														$solid_button   = explode( ' ', trim( $s_item['solid_button']['value'] ) )[0];
														$solid_button   = 'Yes' === $solid_button ? 'Solid Button' : 'Z-Line';
														$hoods_color    = $s_item['color']['value'];
														echo '<p>' . esc_html( $trim_options ) . '</p>';
														echo '<p>Hood Width: ' . esc_html( $height_width[0] ) . '</p>';
														echo '<p>Hood Height: ' . esc_html( $height_width[1] ) . '</p>';
														echo '<p>Hood Depth: ' . esc_html( isset( $depth[0] ) ? $depth[0] : '' ) . '</p>';
													}
													?>
												</div>
												<div class="syle_finish_sec">
													<h4 class="style_head"><?php esc_html_e( 'Hood Finish', 'wilkeshub' ); ?></h4>
													<p><?php esc_html_e( 'Hood Color: ', 'wilkeshub' ); ?><?php echo esc_html( $hoods_color ); ?></p>
													<h4 class="style_head"><?php esc_html_e( 'What needs to be replaced', 'wilkeshub' ); ?></h4>
													<?php
													if ( 'Wood Hoods' === $damage_item || 'Island Wood Hoods' === $damage_item || 'Quick Shipping' ) :
														?>
														<p><?php echo esc_html( $hood_replace ); ?></p>
													<?php elseif ( 'Floating Shelves' === $damage_item ) : ?>
														<p><?php echo esc_html( $f_shelf_replace ); ?></p>
													<?php elseif ( 'Hall Trees' === $damage_item ) : ?>
														<p><?php echo esc_html( $hall_tree_replace ); ?></p>
													<?php else : ?>
														<p><?php echo esc_html( $no_replace ); ?></p>
													<?php endif; ?>
													<h4 class="style_head"><?php esc_html_e( 'Ventilation Information', 'wilkeshub' ); ?></h4>
													<?php
													foreach ( $line_items['line_items'] as $key => $s_item ) {
														echo '<p>Liner: ' . esc_html( $s_item['tradewinds_sku'] ) . '</p>';
													}
													?>
													<p><?php esc_html_e( 'Duct Kit: ', 'wilkeshub' ); ?><?php echo '5BL-39-400-DCK'; ?></p>
													<p><?php esc_html_e( 'Recircuiting Vent Slots: ', 'wilkeshub' ); ?><?php echo 'Yes'; ?></p>
												</div>
											</div>
											<div class="product_style_info">
											<div class="syle_finish_sec">
													<h4 class="style_head"><?php esc_html_e( 'Modifications', 'wilkeshub' ); ?></h4>
													<p><?php esc_html_e( 'Reduced Height: ', 'wilkeshub' ); ?><?php echo esc_html( $reduce_height ); ?></p>
													<p><?php esc_html_e( 'Molding/Strapping: ', 'wilkeshub' ); ?><?php echo esc_html( $crown_molding ); ?></p>
													<h4 class="style_head"><?php esc_html_e( 'Accessories', 'wilkeshub' ); ?></h4>
													<p><?php esc_html_e( 'Crown Molding: ', 'wilkeshub' ); ?><?php echo esc_html( $crown_molding ); ?></p>
												</div>
												<div class="syle_finish_sec">
													<h4 class="style_head"><?php esc_html_e( 'Shipping Details', 'wilkeshub' ); ?></h4>
													<?php echo '<p>'.$address_1.' '.$address_2.', '.$city.', '.$state.', '.$postcode.', '.$country.'</p>'; ?>
												</div>
												<div class="syle_finish_sec">
													<h4 class="style_head"><?php esc_html_e( 'Hoodsly Notes', 'wilkeshub' ); ?></h4>
													<p><?php echo esc_html( $customer_note ); ?></p>
												</div>
											</div>
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
						<?php
						// } // end  if
					} // end while
				} else {
					?>
					<tr style="background-color: #4747471a;">
						<td colspan="100%" class="text-left"><?php esc_html_e( 'There is no order yet', 'wilkeshub' ); ?></td>
					</tr>
					<?php
				} // end if
				wp_reset_postdata();
				?>
				</tbody>
			</table>
			<div class="hub-pagination" id="wilkesPaginate" data-max_num_pages="<?php echo intval( $all_orders->max_num_pages ); ?>">
				<?php
				shop_pagination( $hub_paged, $all_orders->max_num_pages ); // Pagination Function
				?>
			</div>
		</div>
	</div>
</div>
