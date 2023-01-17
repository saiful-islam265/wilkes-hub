<div class="col-xl-12">
	<div class="dashboard-page__order">
		<div class="dashboard-page__order-header">
			<div class="navbar-header">
				<h6 class="title"><?php wp_title( '' ); ?></h6>
			</div>
			<div class="navbar-collapse">
				<ul class="navbar-nav ml-auto">
					<li class="dropdown">
						<a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Create</a>
						<ul class="dropdown-menu">
							<li><a href="#">New Order</a></li>
							<li><a href="#">Create Invoice</a></li>
						</ul>
					</li>
					<li><a href="#" class="icon-setting-sliders"></a></li>
				</ul>
			</div>
		</div>
		<div class="dashboard-page__order-body" id="vent-order-list">
			<table class="table table-order">
				<thead>
				<tr>
					<th scope="col">Order Id</th>
					<th scope="col">Customer Info</th>
					<th scope="col">Order Status</th>
					<th scope="col">Estimated Shipping Date</th>
					<th scope="col">Order Source</th>
					<th scope="col">Ventilation SKU</th>
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
							'key'     => 'is_tradewinds_selected',
							'value'   => 'yes',
							'compare' => 'LIKE',
						),
						array(
							'key'     => 'action',
							'value'   => 'Delivered',
							'compare' => 'NOT EXISTS',
						),
					),
				);
				$all_orders             = new WP_Query( $args );
				if ( $all_orders->have_posts() ) {
					while ( $all_orders->have_posts() ) {
						$all_orders->the_post();
						$shipping_add            = get_post_meta( get_the_ID(), 'shipping', true );
						$shipping                = ( isset( $shipping_add ) && is_array( $shipping_add ) ) ? $shipping_add : array();
						$first_name              = ( isset( $shipping['first_name'] ) && ! empty( $shipping['first_name'] ) ) ? $shipping['first_name'] : '';
						$last_name               = ( isset( $shipping['last_name'] ) && ! empty( $shipping['last_name'] ) ) ? $shipping['last_name'] : '';
						$order_status            = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
						$assign_shop             = trim( get_post_meta( get_the_ID(), 'shop', true ) );
						$shop                    = ( isset( $assign_shop ) && ! empty( $assign_shop ) ) ? $assign_shop : 'Not Assigned Yet';
						$es_shipping_date        = get_post_meta( get_the_ID(), 'estimated_shipping_date', true );
						$shipping_date           = gmdate( 'F jS Y', strtotime( $es_shipping_date ) );
						$estimated_shipping_date = $shipping_date ?? '';
						$origin                  = get_post_meta( get_the_ID(), 'origin', true );
						$domain_parts            = explode( '.', $origin );
						$order_link              = get_template_link( 't_order-details.php' );
						$order_id                = get_post_meta( get_the_ID(), 'order_id', true );
						$line_items              = get_post_meta( get_the_ID(), 'line_items', true );
						$current_date            = gmdate( 'm/d/Y H:i:s', time() );
						$date1                   = strtotime( $estimated_shipping_date );
						$date2                   = strtotime( $current_date );
						$date_difference         = $date1 - $date2;
						$bill_of_landing_id      = intval( get_post_meta( get_the_ID(), 'bill_of_landing_id', true ) );
						$bol_link                = home_url() . '/wp-content/uploads/bol/' . $bill_of_landing_id . '.pdf';
						$shipping_file_link      = home_url() . '/wp-content/uploads/bol/shipping_label_' . $bill_of_landing_id . '.pdf';
						$backgroundg_color       = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
						$result                  = round( $date_difference / ( 60 * 60 * 24 ) );
						?>
						<tr style="background-color: #4747471a;">
							<td data-title="Order Id"><a href="
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
							<td data-title="Customer Info"><?php echo esc_html( $first_name ) . ' ' . esc_html( $last_name ); ?></td>
							<td data-title="Order Status">
								<button class="btn btn-violet"<?php echo esc_attr( $backgroundg_color ); ?>>
									<?php echo esc_html( $order_status ); ?>
								</button>
							</td>
							<td data-title="Estimated Shipping Date">
								<?php echo esc_html( $estimated_shipping_date ) . ' (' . esc_html( $result ) . ' Days)'; ?>
							</td>
							<td data-title="Order Source">
								<?php
								echo esc_html(
									ucfirst(
										$domain_parts[0]
									)
								)
								?>
							</td>
							<td data-title="Items" id="ordered_items">
								<?php
								$product_cat = get_post_meta( get_the_ID(), 'product_cat', true );

								if ( 'tradewinds-inserts' === $product_cat[0] ) {
									foreach ( $line_items['line_items'] as $key => $value ) {
										if ( isset( $value['tradewinds_cat_sku'] ) ) :
											echo esc_html( $value['tradewinds_cat_sku'] );
										endif;
									}
								} else {
									foreach ( $line_items['line_items'] as $key => $value ) {
										if ( isset( $value['tradewinds_sku'] ) ) :
											echo esc_html( $value['tradewinds_sku'] );
										endif;
									}
								}
								?>
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
															"><?php esc_html_e( 'View', 'hoodslyhub' ); ?></a></li>

										<li>
											<a href="#" data-orderid="<?php echo get_the_ID(); ?>" class="hoodslyhub-delete-order" data-nonce="<?php echo esc_attr( wp_create_nonce( 'hoodslyhub_delete_order_nonce' ) ); ?>">
												<?php esc_html_e( 'Delete', 'hoodslyhub' ); ?>
											</a>
										</li>
									<?php if ( 'In Production' === $order_status ) : ?>
											<li>
												<a href="#" data-postid="<?php echo get_the_ID(); ?>" data-orderid="<?php echo esc_html( $order_id ); ?>" class="hoodslyhub-order-hold" data-nonce="<?php echo esc_attr( wp_create_nonce( 'hoodslyhub_order_hold_nonce' ) ); ?>">
													<?php esc_html_e( 'Order Hold', 'hoodslyhub' ); ?>
												</a>
											</li>
									<?php endif; ?>
								</ul>
							</td>
						</tr>
						<?php
					} // end while
				} else {
					?>
					<tr style="background-color: #4747471a;">
						<td colspan="100%" class="text-left"><?php esc_html_e( 'There is no order yet', 'wilkes-hub' ); ?></td>
					</tr>
					<?php
				} // end if
				wp_reset_postdata();
				?>
				</tbody>
			</table>
			<div class="hub-pagination" id="ventPaginate" data-max_num_pages="<?php echo $all_orders->max_num_pages; ?>">
				<?php
				shop_pagination( $hub_paged, $all_orders->max_num_pages ); // Pagination Function
				?>
			</div>
		</div>
	</div>
</div>
