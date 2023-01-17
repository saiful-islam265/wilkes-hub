<div class="col-xl-6">
	<div class="dashboard-page__order">
		<div class="dashboard-page__order-header">
			<div class="navbar-header">
				<h6 class="title"><?php esc_html_e( 'Custom Color Match Orders', 'hoodslyhub' ); ?></h6>
			</div>

			<div class="navbar-collapse">
				<ul class="navbar-nav ml-auto">
					<li><a href="#" class="icon-setting-sliders"></a></li>
				</ul>
			</div>
		</div>
		<div class="dashboard-page__order-body" id="ccm-order-list">

			<table class="table has--custom-color">
				<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Order Id', 'hoodslyhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Date Placed', 'hoodslyhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Sample Status', 'hoodslyhub' ); ?></th>
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
							'value'   => '1',
							'compare' => 'LIKE',
						),
						array(
							'key'     => 'order_status',
							'value'   => 'Delivered',
							'compare' => 'LIKE',
						),
					),
				);
				$custom_color_orders    = new WP_Query( $args );
				if ( $custom_color_orders->have_posts() ) {
					while ( $custom_color_orders->have_posts() ) {
						$custom_color_orders->the_post();
						$order_id                  = get_post_meta( get_the_ID(), 'order_id', true );
						$first_name                = get_post_meta( get_the_ID(), 'first_name', true );
						$last_name                 = get_post_meta( get_the_ID(), 'last_name', true );
						$order_status              = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
						$order_date                = trim( get_post_meta( get_the_ID(), 'order_date', true ) );
						$date_placed               = date( 'F jS Y', strtotime( $order_date ) );
						$samples_status            = trim( get_post_meta( get_the_ID(), 'samples_status', true ) );
						$custom_color_match_status = trim( get_post_meta( get_the_ID(), 'custom_color_match_status', true ) );
						$origin                    = get_post_meta( get_the_ID(), 'origin', true );
						$status_color              = ( 'Received' === $samples_status ) ? 'style=background-color:#A8DCD7' : ( ( 'Send To Be Matched' === $samples_status ) ? 'style=background-color:#9dd5f0' : ( ( 'Delivered' === $samples_status ) ? 'style=background-color:#BEA8DC' : 'style=background-color:#F09D9D' ) );
						$dropdown                  = ( 'Delivered' === $custom_color_match_status ) ? 'dropdown' : '';
						?>
							<tr style="background-color: #0E9CEE1a;">
								<td data-title="Order Id">#<?php the_title(); ?></td>
								<td data-title="Order Date">
									<?php echo esc_html( $date_placed ); ?>
								</td>
								<td data-title="Status" class="staus-dropdown <?php echo esc_attr( $dropdown ); ?>">
									<button role="button" class="btn btn-waiting"<?php echo esc_attr( $status_color ); ?>
											data-toggle="<?php echo esc_attr( $dropdown ); ?>">
										<?php echo esc_html( $samples_status ); ?>
									</button>
									<?php if ( 'Delivered' === $samples_status ) : ?>
										<ul class="dropdown-menu dropdown-menu-right">
											<li>
												<a href="#" data-postid="<?php echo get_the_ID(); ?>" class="wilkes_ccm_received"
												   data-nonce="<?php echo esc_attr( wp_create_nonce( 'wilkes_received_nonce' ) ); ?>">
													<?php esc_html_e( 'Received', 'hoodslyhub' ); ?>
												</a>
											</li>
										</ul>
									<?php elseif ( 'Received' === $samples_status ) : ?>
										<ul class="dropdown-menu dropdown-menu-right">
											<li>
												<a href="#" data-postid="<?php echo get_the_ID(); ?>" class="ccm_send_to_be_matched" data-nonce="<?php echo esc_attr( wp_create_nonce( 'ccm_sent_to_be_matched_nonce' ) ); ?>">
													<?php esc_html_e( 'Send To Be Matched', 'hoodslyhub' ); ?>
												</a>
											</li>
										</ul>
									<?php else : ?>
										<ul class="dropdown-menu dropdown-menu-right">
											<li>
												<a href="#" data-postid="<?php echo get_the_ID(); ?>" class="ccm_matched" data-nonce="<?php echo esc_attr( wp_create_nonce( 'ccm_matched_nonce' ) ); ?>">
													<?php esc_html_e( 'Matched', 'hoodslyhub' ); ?>
												</a>
											</li>
										</ul>
									<?php endif; ?>
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
			<div class="hub-pagination" id="ccmPaginate" data-max_num_pages="<?php echo $custom_color_orders->max_num_pages; ?>">
				<?php
				shop_pagination( $hub_paged, $custom_color_orders->max_num_pages ); // Pagination Function
				?>
			</div>
		</div>
	</div>
</div>
