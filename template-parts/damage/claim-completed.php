<div class="col-xl-12 d-flex">
	<div class="dashboard-page__order">
		<div class="dashboard-page__order-header">
			<div class="navbar-header">
				<h6 class="title"><?php echo esc_html__( 'Approved Claim', 'wilkeshub' ); ?></h6>
			</div>

			<div class="navbar-collapse">
				<ul class="navbar-nav ml-auto">
					<li class="dropdown">
						<a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Create</a>
					</li>
					<li><a href="#" class="icon-setting-sliders"></a></li>
				</ul>
			</div>
		</div>
		<div class="dashboard-page__order-body" id="damage-claim-list">
			<table class="table table-order">
				<thead>
				<tr>
					<th scope="col">Order Id</th>
					<th scope="col">Customer Info</th>
					<th scope="col">Claim #</th>
					<th scope="col">Value</th>
					<th scope="col">Action Requested</th>
					<th scope="col">Order Source</th>
					<th scope="col">Type</th>
					<th scope="col">Proof</th>
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
							'key'     => 'shop_claim',
							'value'   => 'approved',
							'compare' => 'LIKE',
						),
					),
				);
				$all_orders             = new WP_Query( $args );
				if ( $all_orders->have_posts() ) {

					while ( $all_orders->have_posts() ) {
						$all_orders->the_post();
						$shipping_add              = get_post_meta( get_the_ID(), 'shipping', true );
						$shipping                  = ( isset( $shipping_add ) && is_array( $shipping_add ) ) ? $shipping_add : array();
						$first_name                = ( isset( $shipping['first_name'] ) && ! empty( $shipping['first_name'] ) ) ? $shipping['first_name'] : '';
						$last_name                 = ( isset( $shipping['last_name'] ) && ! empty( $shipping['last_name'] ) ) ? $shipping['last_name'] : '';
						$origin                    = get_post_meta( get_the_ID(), 'origin', true );
						$domain_parts              = explode( '.', $origin );
						$order_link                = get_template_link( 't_order-details.php' );
						$order_id                  = get_post_meta( get_the_ID(), 'order_id', true );
						$damage_claim_id           = get_post_meta( get_the_ID(), 'damage_claim_id', true );
						$damage_item               = get_post_meta( get_the_ID(), 'damage_item', true );
						$damage_type               = get_post_meta( get_the_ID(), 'damage_type', true );
						$damage_details            = get_post_meta( get_the_ID(), 'damage_details', true );
						$damage_claim_filling_date = get_post_meta( get_the_ID(), 'damage_claim_filling_date', true );
						$damage_proof_submit_date  = get_post_meta( get_the_ID(), 'damage_proof_submit_date', true );
						$damage_image_src          = get_post_meta( get_the_ID(), 'damage_image_src', true );
						$claim_value               = get_post_meta( get_the_ID(), 'claim_value', true );
						$hood_replace              = get_post_meta( get_the_ID(), 'hood_replace', true );
						$f_shelf_replace           = get_post_meta( get_the_ID(), 'f_shelf_replace', true );
						$hall_tree_replace         = get_post_meta( get_the_ID(), 'hall_tree_replace', true );
						$no_replace                = get_post_meta( get_the_ID(), 'no_replace', true );
						$damage_image_src          = get_post_meta( get_the_ID(), 'damage_image_src', true );


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

							</td>
							<td data-title="Customer Info"><?php echo esc_html( $first_name ) . ' ' . esc_html( $last_name ); ?></td>
							<td data-title="Order Status">
								<button class="btn btn-violet">
									<?php echo esc_html( $damage_claim_id ); ?>
								</button>
							</td>
							<td data-title="Value">
								$<?php echo esc_html( $claim_value ); ?>
							</td>
							<?php
							if ( 'Wood Hoods' === $damage_item || 'Island Wood Hoods' === $damage_item || 'Quick Shipping' ) :
								?>
								<td data-title="Action Requested">
									<?php echo esc_html( $hood_replace ); ?>
								</td>
							<?php elseif ( 'Floating Shelves' === $damage_item ) : ?>
								<td data-title="Action Requested">
									<?php echo esc_html( $f_shelf_replace ); ?>
								</td>
							<?php elseif ( 'Hall Trees' === $damage_item ) : ?>
								<td data-title="Action Requested">
									<?php echo esc_html( $hall_tree_replace ); ?>
								</td>
							<?php else : ?>
								<td data-title="Action Requested">
									<?php echo esc_html( $no_replace ); ?>
								</td>
							<?php endif; ?>
							<td data-title="Order Source">
								<?php
								echo esc_html(
									ucfirst(
										$domain_parts[0]
									)
								)
								?>
							</td>
							<td data-title="Type">
								<?php echo esc_html( $damage_type ); ?>
							</td>
							<td class="files" data-title="Files" data-toggle="collapse" data-target="#cliam-<?php echo intval( $order_id ); ?>"
								aria-expanded="false" aria-controls="cliam-<?php echo intval( $order_id ); ?>">
								<?php esc_html_e( 'Files', 'wilkeshub' ); ?>
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
															"><?php esc_html_e( 'View', 'wilkeshub' ); ?></a></li>
										<li>
											<a href="#" data-orderid="<?php echo get_the_ID(); ?>" class="wilkeshub-delete-order" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wilkeshub_delete_order_nonce' ) ); ?>">
												<?php esc_html_e( 'Delete', 'wilkeshub' ); ?>
											</a>
										</li>
								</ul>
							</td>
						</tr>
						<tr class="cliam-collapse">
							<td colspan="12">
								<div class="cliam-collapse__body collapse" id="cliam-<?php echo intval( $order_id ); ?>">
									<div class="row">
										<div class="col-xl-6 col-lg-12">
											<div class="cliam-collapse__body-content">
												<div class="cliam-simplebar" data-simplebar="init">
													<ul class="list-unstyled">
														<?php $current_cms = wp_get_current_user(); ?>
														<li><strong><?php echo esc_html__( 'File Date: ', 'wilkeshub' ); ?></strong>
															<?php echo esc_html( $damage_claim_filling_date ); ?>
														</li>
														<li><strong><?php echo esc_html__( 'Filed By: ', 'wilkeshub' ); ?></strong>
															<?php echo esc_html( $current_cms->user_login ); ?>
														</li>
														<li><strong><?php echo esc_html__( 'Proof Sub. Date: ', 'wilkeshub' ); ?></strong>
															<?php echo esc_html( $damage_proof_submit_date ); ?>
														</li>
													</ul>

													<div class="text">
														<h6 class="title"><?php echo esc_html__( 'Details Described in Claims', 'wilkeshub' ); ?></h6>
														<p><?php echo esc_html( $damage_details ); ?></p>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xl-4 col-lg-12">
											<div class="cliam-collapse__body-view">
												<div class="cliam-simplebar" data-simplebar="init">
													<h6 class="title"><?php echo esc_html__( 'Damage Claims viewed:', 'wilkeshub' ); ?></h6>
													<div class="row popup-gallery last-none ">
														<?php foreach ( $damage_image_src as $image ) : ?>
															<div class="col-xl-4 col-lg-3 col-md-4 col-sm-6 col-6">
																<a href="<?php echo esc_url( $image ); ?>" class="gallery-popup-item">
																	<figure class="media">
																		<img src="<?php echo esc_url( $image ); ?>" class="img-fluid" alt="">
																	</figure>
																</a>
															</div>

														<?php endforeach; ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
						<!-- Shop claim Modal -->

						<?php

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
			<div class="hub-pagination" id="damageclaimPaginate" data-max_num_pages="<?php echo $all_orders->max_num_pages; ?>">
				<?php
				shop_pagination( $hub_paged, $all_orders->max_num_pages ); // Pagination Function
				?>
			</div>
		</div>

	</div>

</div>

