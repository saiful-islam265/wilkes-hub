<?php
/*
Template Name: Accessory Orders
*/
if ( is_user_logged_in() ) :
	get_header();
	?>
	<div class="dashboard-page">
		<div class="container-fluid">
			<div class="row lr-12">
				<div class="col-xl-12">
					<div class="dashboard-page__order">
						<div class="dashboard-page__order-header">
							<div class="navbar-header">
								<h6 class="title"><?php esc_html_e( 'Accessory Orders', 'hoodslyhub' ); ?></h6>
							</div>
						</div>
						<div class="dashboard-page__order-body">
							<table class="table table-order">
								<thead>
								<tr>
									<th scope="col">Order Title</th>
									<th scope="col">Shipping Label</th>
									<th scope="col">Order #</th>
									<th scope="col">Order Detail</th>
									<th scope="col">Molding MA S SM</th>
									<th scope="col">Priority</th>
									<th scope="col">Additional Notes</th>
									<th scope="col">Work Order</th>
									<th scope="col">Mark Completed/Shipped</th>
								</tr>
								</thead>
								<tbody>
								<?php
								$args         = array(
									'post_type'      => 'accessory_orders',
									'posts_per_page' => - 1,
									'orderby'        => 'ID',
								);
								$all_invoices = new WP_Query( $args );
								if ( $all_invoices->have_posts() ) {
									while ( $all_invoices->have_posts() ) {
										$all_invoices->the_post();

										$order_id       = get_the_title();
										$order_title = get_post_meta(get_the_ID(), 'order_title', true);
										$molding_ma_s_sm = get_post_meta(get_the_ID(), 'molding_ma_s_sm', true);
										$order_priority = get_post_meta(get_the_ID(), 'order_priority', true);
										$additional_notes = get_post_meta(get_the_ID(), 'additional_notes', true);
										$add_fees = get_post_meta(get_the_ID(), 'add_fees', true);
										$order_status = get_post_meta(get_the_ID(), 'order_status', true);
										$attachment_file_url = get_post_meta(get_the_ID(), 'attachment_file_url', true);
										$order_priority = isset($order_priority) ? $order_priority : '';
										?>
											<tr style="background-color: #4747471a;">
												<td data-title="Invoice ID"><?php echo $order_title; ?></td>
												<td data-title="Shipping Label"><a href="<?php echo $attachment_file_url; ?>" style="color: #fff;" class="btn btn-success">Download Label</a></td>
												<td data-title="Order Id"><a href="#"><?php echo $order_id; ?></a></td>
												<td data-title="Order Detail" class="item_detail"><a href="#" style="color: #fff;" class="btn btn-primary" data-toggle="modal" data-target="#order_<?php echo $order_id; ?>">Details</a>
													<div class="modal fade" id="order_<?php echo $order_id; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
														<div class="modal-dialog modal-dialog-centered modal-lg">
															<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title" id="exampleModalLabel">Order Quick Details</h5>
																<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																<span aria-hidden="true">&times;</span>
																</button>
															</div>
															<div class="modal-body">
																<div class="order_details_section">
																	<?php
																		$order_details = get_post_meta(get_the_ID(), 'order_details', true);
																		echo '<p>* Product Name ----------------------- '.strip_tags($order_details['line_items'][0]['product_name']).'</p>';
																			echo '<p>* Product ID ----------------------- '.strip_tags($order_details['line_items'][0]['product_id']).'</p>';
																		foreach ($order_details['line_items'][0]['order_meta'] as $key => $value) {
																			echo '<p>* '.$value['display_key'].' ----------------------- '.strip_tags($value['display_value']).'</p>';
																		}
																	?>
																	<p></p>
																</div>
															</div>
															<div class="modal-footer">
																<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
															</div>
															</div>
														</div>
													</div>
												</td>
												<td data-title="Order Id"><a href="#"><?php echo $molding_ma_s_sm; ?></a></td>
												<td data-title="Order Id"><?php echo $order_priority; ?></td>
												<td data-title="Additional Notes"><?php echo $additional_notes; ?></td>
												<td data-title="Work order"><button class="btn btn-success print_order" data-orderdata="<?php echo $order_id; ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'print_details' ) ); ?>">Print</button></td>
												<td data-title="Order Status"><a href="#" style="color: #fff;" class="btn btn-success">Mark Order Complete</a></td>
											</tr>
											<?php
									} // end while
								} // end if
								wp_reset_query();
								?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
	get_footer();
else :
	wp_safe_redirect( wp_login_url() );
	die;
endif;
