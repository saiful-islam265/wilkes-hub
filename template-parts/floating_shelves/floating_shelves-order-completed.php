<div class="col-xl-12 ">
	<div class="dashboard-page__order">
		<div class="dashboard-page__order-header">
			<div class="navbar-header">
				<h6 class="title">Completed Orders</h6>
			</div>

			<div class="navbar-collapse">
				<ul class="navbar-nav ml-auto">
					<li><a href="#" class="icon-setting-sliders"></a></li>
				</ul>
			</div>
		</div>
		<div class="dashboard-page__order-body" id="completed-floating-order-list">
			<table class="table table-order">
				<thead>
				<tr>
					<th scope="col">Order</th>
					<th scope="col">Customer Info</th>
					<th scope="col">Order Status</th>
					<th scope="col">Order Source</th>
					<th scope="col">Priority</th>
					<th scope="col">Ship Method</th>
					<th scope="col">Shipping Label</th>
					<th scope="col">Shelf QTY</th>
					<th scope="col">Items</th>
					<th scope="col">Actions</th>
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
							'key'     => 'product_cat',
							'value'   => 'floating-shelves',
							'compare' => 'LIKE',
						),
						array(
							'key'     => 'order_status',
							'value'   => 'Shipped',
							'compare' => 'AND',
						),
					),
				);
				$all_orders             = new WP_Query( $args );
				if ( $all_orders->have_posts() ) {
					while ( $all_orders->have_posts() ) {
						$all_orders->the_post();
						$product_cat = get_post_meta( get_the_ID(), 'product_cat', true );

						$bill_of_landing_id      = intval( get_post_meta( get_the_ID(), 'bill_of_landing_id', true ) );
						$shipping_add            = get_post_meta( get_the_ID(), 'shipping', true );
						$shipping                = ( isset( $shipping_add ) && is_array( $shipping_add ) ) ? $shipping_add : array();
						$first_name              = ( isset( $shipping['first_name'] ) && ! empty( $shipping['first_name'] ) ) ? $shipping['first_name'] : '';
						$last_name               = ( isset( $shipping['last_name'] ) && ! empty( $shipping['last_name'] ) ) ? $shipping['last_name'] : '';
						$order_status            = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
						$es_shipping_date        = get_post_meta( get_the_ID(), 'estimated_shipping_date', true );
						$shipping_date           = date( 'F jS Y', strtotime( $es_shipping_date ) );
						$estimated_shipping_date = $shipping_date ?? '';
						$origin                  = get_post_meta( get_the_ID(), 'origin', true );
						$shipping_lines          = get_post_meta( get_the_ID(), 'shipping_lines', true );
						$domain_parts            = explode( '.', $origin );
						$order_link              = get_template_link( 't_order-details.php' );
						$order_id                = get_post_meta( get_the_ID(), 'order_id', true );
						$line_items_arr          = get_post_meta( get_the_ID(), 'line_items', true );
						$line_items              = isset( $line_items_arr ) && is_array( $line_items_arr ) ? $line_items_arr : array();
						$is_priority             = get_post_meta( get_the_ID(), 'is_priority', true );
						$damage_item             = get_post_meta( get_the_ID(), 'damage_item', true );
						$hood_replace            = get_post_meta( get_the_ID(), 'hood_replace', true );
						$f_shelf_replace         = get_post_meta( get_the_ID(), 'f_shelf_replace', true );
						$hall_tree_replace       = get_post_meta( get_the_ID(), 'hall_tree_replace', true );
						$no_replace              = get_post_meta( get_the_ID(), 'no_replace', true );
						$bol_link                = home_url() . '/wp-content/uploads/bol/' . $bill_of_landing_id . '.pdf';
						$shipping_file_link      = home_url() . '/wp-content/uploads/bol/shipping_label_' . $bill_of_landing_id . '.pdf';
						$backgroundg_color       = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : ( ( 'Shipped' === $order_status ) ? 'style=background-color:#12cc31' : '' ) ) ) ) ) ) );
						$bol_regenerated         = get_post_meta( get_the_ID(), 'bol_regenerated', true );
						$checked                 = ( 'yes' === $is_priority ) ? 'checked' : '';
						$total_qty               = 0;
						foreach ( $line_items['line_items'] as $s_item ) {
							$total_qty += $s_item['quantity'];
						}
						?>

						<tr style="background-color: #dafbde;">
							<td data-title="Order Id">
							<input type="checkbox" class="bulk_check" value="test" data-orderid="<?php echo esc_html( $order_id ); ?>" data-postid="<?php echo get_the_ID(); ?>"
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
								"><?php the_title(); ?></a></td>
							<td data-title="Customer Info"><?php echo esc_html( $first_name ) . ' ' . esc_html( $last_name ); ?></td>
							<td data-title="Order Status">
								<button class="btn btn-violet" <?php echo esc_attr( $backgroundg_color ); ?>>
									<?php echo esc_html( $order_status ); ?>
								</button>
							</td>
							<td data-title="order source"><?php echo esc_html( $origin ); ?></td>
							<td data-title="priority"><?php esc_html_e( 'Normal', 'wilkeshub' ); ?></td>
							<td data-title="Ship Method"><?php echo 'UPS'; ?></td>
							<td data-title="Shipping Label"><button class="btn btn-success print_order" data-orderdata="<?php echo esc_html( $order_id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'print_details' ) ); ?>">Print</button></td>
							<td data-title="Self QTY"><?php echo intval( $total_qty ); ?></td>
							<td data-title="Order Notes">
								<button class="btn btn-border" type="button" data-toggle="collapse"
										data-target="#notes-<?php echo esc_html( $order_id ); ?>"
										aria-expanded="false" aria-controls="notes-<?php echo esc_html( $order_id ); ?>"><?php esc_html_e( 'Item Detail', 'wilkeshub' ); ?>
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

										<li><a href="#" data-orderid="<?php echo get_the_ID(); ?>" class="wilkeshub-delete-order" data-nonce="<?php echo wp_create_nonce( 'wilkeshub_delete_order_nonce' ); ?>">Delete</a>
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
											<div class="product_style_info">
												<?php
												$line_items_arr = get_post_meta( get_the_ID(), 'line_items', true );
												$line_items     = isset( $line_items_arr ) && is_array( $line_items_arr ) ? $line_items_arr : array();
												$customer_note  = get_post_meta( get_the_ID(), 'customer_note', true );
												foreach ( $line_items['line_items'] as $key => $s_item ) {
													?>
													<div class="syle_finish_sec">
													<h4 class="style_head">Floating Shelves Overview</h4>
													<?php
														$hoods_color = $s_item['color']['value'];
														echo '<p>Quantity: ' . intval( $s_item['quantity'] ) . '</p>';
														echo '<p>Color: ' . esc_html( $hoods_color ) . '</p>';
														echo '<p>Width: ' . esc_html( isset( $s_item['float_width']['value'] ) ? $s_item['float_width']['value'] : '' ) . '</p>';
														echo '<p>Depth: ' . esc_html( isset( $s_item['float_depth']['value'] ) ? $s_item['float_depth']['value'] : '' ) . '</p>';
														echo '<p>Thickness: ' . esc_html( isset( $s_item['float_thick']['value'] ) ? $s_item['float_thick']['value'] : '' ) . '</p>';
													?>

													</div>
													<?php
												}
												?>
											</div>
											<div class="product_style_info">
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
				}
				wp_reset_postdata();
				?>
				</tbody>
			</table>
			<div class="hub-pagination" id="completedfloatingPaginate" data-max_num_pages="<?php echo $all_orders->max_num_pages; ?>">
				<?php
				shop_pagination( $hub_paged, $all_orders->max_num_pages ); // Pagination Function
				?>
			</div>
		</div>
	</div>
</div>
