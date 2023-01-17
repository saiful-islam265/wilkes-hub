<div class="col-xl-6">
	<div class="dashboard-page__order">
		<div class="dashboard-page__order-header">
			<div class="navbar-header">
				<h6 class="title"><?php esc_html_e( 'Pending Orders', 'hoodslyhub' ); ?></h6>
			</div>

			<div class="navbar-collapse">
				<ul class="navbar-nav ml-auto">
					<li><a href="#" class="icon-setting-sliders"></a></li>
				</ul>
			</div>
		</div>
		<div class="dashboard-page__order-body" id="wilkes-pending-order-list">
			<table class="table has--custom-color">
				<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Order Id', 'hoodslyhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Requested Date', 'hoodslyhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Status', 'hoodslyhub' ); ?></th>
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
							'key'     => 'order_status',
							'value'   => 'On Hold',
							'compare' => 'LIKE',
						),
					),

				);
				$pending_orders = new WP_Query( $args );
				if ( $pending_orders->have_posts() ) {
					while ( $pending_orders->have_posts() ) {
						$pending_orders->the_post();
						$order_id          = get_post_meta( get_the_ID(), 'order_id', true );
						$first_name        = get_post_meta( get_the_ID(), 'first_name', true );
						$last_name         = get_post_meta( get_the_ID(), 'last_name', true );
						$order_status      = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
						$order_date        = trim( get_post_meta( get_the_ID(), 'order_date', true ) );
						$completion_date   = trim( get_post_meta( get_the_ID(), 'completion_date', true ) );
						$date_placed       = date( 'F jS Y', strtotime( $order_date ) );
						$samples_status    = trim( get_post_meta( get_the_ID(), 'samples_status', true ) );
						$origin            = get_post_meta( get_the_ID(), 'origin', true );
						$backgroundg_color = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
						$status_color      = ( 'Received' === $samples_status ) ? 'style=background-color:#A8DCD7' : ( ( 'Picked Up' === $samples_status ) ? 'style=background-color:#DCD8A8' : ( ( 'Delivered' === $samples_status ) ? 'style=background-color:#BEA8DC' : 'style=background-color:#F09D9D' ) );
						?>
							<tr style="background-color: #0E9CEE1a;">
								<td data-title="Order Id">#<?php the_title(); ?></td>
								<td data-title="Order Date">
									<?php echo esc_html( $completion_date ); ?>
								</td>
								<td data-title="Status" class="staus-dropdown dropdown">
									<button role="button" class="btn btn-waiting"<?php echo esc_attr( $status_color ); ?> data-toggle="dropdown">
										<?php echo esc_html( $order_status ); ?>
									</button>
										<?php if ( 'On Hold' === $order_status ) : ?>
											<ul class="dropdown-menu dropdown-menu-right">
											<li>
												<a href="#" data-postid="<?php echo get_the_ID(); ?>" data-orderid="<?php echo intval( $order_id ); ?>" class="wilkes-pending-status-action" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wilkes_order_pending_to_production_nonce' ) ); ?>">
													<?php esc_html_e( 'In Production', 'hoodslyhub' ); ?>
												</a>
											</li>
										<?php endif; ?>
										</ul>
								</td>
							</tr>
							<?php
					}
				} else {
					?>
					<tr style="background-color: #4747471a;">
						<td colspan="100%" class="text-left"><?php esc_html_e( 'There is no order yet', 'wilkes-hub' ); ?></td>
					</tr>
					<?php
				}
				wp_reset_postdata();
				?>
				</tbody>
			</table>
			<div class="hub-pagination" id="wilkespendingPaginate" data-max_num_pages="<?php echo $pending_orders->max_num_pages; ?>">
				<?php
				shop_pagination( $hub_paged, $pending_orders->max_num_pages ); // Pagination Function
				?>
			</div>
		</div>
	</div>
</div>
