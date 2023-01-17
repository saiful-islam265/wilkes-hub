<?php
/*
 * Hub pagination Fuction
 */
if ( ! function_exists( 'shop_pagination' ) ) :

	function shop_pagination( $paged = '', $max_page = '' ) {
		$big = 999999999; // need an unlikely integer
		if ( ! $paged ) {
			$paged = get_query_var( 'paged' );
		}

		if ( ! $max_page ) {
			global $wp_query;
			$max_page = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
		}

		echo paginate_links(
			array(
				'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format'    => '?paged=%#%',
				'current'   => max( 1, $paged ),
				'total'     => $max_page,
				'prev_next' => true,
				'prev_text' => '<span class="pagination paginate-prev"><span><i class="icon-angle-left"></i></span></span> ',
				'next_text' => '<span class="pagination paginate-next ml-auto"><span><i class="icon-angle-right"></i></span></span>',
			)
		);
	}
endif;

/*
 * Hub pagination Fuction
 */
add_action( 'wp_ajax_pagination_ajax', 'wilkes_pagination_ajax' );
function wilkes_pagination_ajax( $paged = '', $max_page = '' ) {

	$hub_paged = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
	$max_page  = ( isset( $_POST['max_page'] ) ) ? $_POST['max_page'] : 1;
	$big       = 999999999; // need an unlikely integer

	echo paginate_links(
		array(
			'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'    => '?paged=%#%',
			'current'   => max( 1, $hub_paged ),
			'total'     => $max_page,
			'prev_next' => true,
			'prev_text' => '<span class="pagination paginate-prev"><span><i class="icon-angle-left"></i></span></span> ',
			'next_text' => '<span class="pagination paginate-next ml-auto"><span><i class="icon-angle-right"></i></span></span>',
		)
	);
	die();
}

/*
 * wilkes order table pagination
 */
add_action( 'wp_ajax_wilkes_order_table_pagination', 'hub_wilkes_order_table_pagination' );
function hub_wilkes_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
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
			$backgroundg_color           = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
			$bol_regenerated             = get_post_meta( get_the_ID(), 'bol_regenerated', true );
			$checked                     = ( 'yes' === $is_priority ) ? 'checked' : '';
			$disabled                    = '';
			if ( 'rush_my_order' === $is_priority ) {
				$is_priority = '';
			} else {
				$is_priority = 'text-decoration-line: line-through';
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
								"><?php the_title(); ?></a></td>
				<td data-title="Customer Info"><?php echo esc_html( $first_name ) . ' ' . esc_html( $last_name ); ?></td>
				<td data-title="Order Status">
					<button class="btn btn-violet" <?php echo esc_attr( $backgroundg_color ); ?>>
						<?php echo esc_html( $order_status ); ?>
					</button>
				</td>
				<td data-title="order source"><?php echo esc_html( $origin ); ?></td>

				<td data-title="Free Curbside Delivery"><button class="btn btn-danger <?php echo esc_attr( $disabled ); ?>" style="<?php echo esc_attr( $is_priority ); ?>">Priority</button></td>
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
	}
	wp_reset_postdata();
}

/*
 * Pending order table pagination
 */
add_action( 'wp_ajax_pending_order_table_pagination', 'hub_pending_order_table_pagination' );
function hub_pending_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
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
	}
	wp_reset_postdata();
}

/*
 * Completed order table pagination
 */
add_action( 'wp_ajax_completed_order_table_pagination', 'hub_completed_order_table_pagination' );
function hub_completed_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
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
				'value'   => 'Delivered',
				'compare' => 'LIKE',
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
			$backgroundg_color           = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
			$bol_regenerated             = get_post_meta( get_the_ID(), 'bol_regenerated', true );
			$checked                     = ( 'yes' === $is_priority ) ? 'checked' : '';
			$disabled                    = '';
			if ( 'rush_my_order' === $is_priority ) {
				$is_priority = '';
			} else {
				$is_priority = 'text-decoration-line: line-through';
				$disabled    = 'disabled';
			}
			?>
            <tr style="background-color: #dafbde;">
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

                <td data-title="Free Curbside Delivery"><button class="btn btn-danger <?php echo esc_attr( $disabled ); ?>" style="<?php echo esc_attr( $is_priority ); ?>">Priority</button></td>
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
		} // end while
	}
	wp_reset_postdata();
}

/*
 * CCM order table pagination
 */
add_action( 'wp_ajax_ccm_order_table_pagination', 'hub_ccm_order_table_pagination' );
function hub_ccm_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
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
	}
	wp_reset_postdata();
}

/*
 * Vent  order table pagination
 */
add_action( 'wp_ajax_vent_order_table_pagination', 'hub_vent_order_table_pagination' );
function hub_vent_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
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
	} // end if
	wp_reset_postdata();
}

/*
 * Vent completed order table pagination
 */
add_action( 'wp_ajax_vent_completed_order_table_pagination', 'hub_vent_completed_order_table_pagination' );
function hub_vent_completed_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
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
				'compare' => 'LIKE',
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
                <td data-title="Shop" data-toggle="tooltip"
                    title='<h6 class="title"><?php echo esc_html( $shop ); ?></h6>'>
                    <figure class="media-shop">
						<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( get_theme_file_uri( 'assets/images/ryan.jpg' ) ), esc_html( get_bloginfo( 'name' ) ) ); ?>
                    </figure>
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
                    <button type="button" class="btn" data-toggle="modal"
                            data-target="#test_<?php echo esc_html( $order_id ); ?>">
						<?php echo isset( $line_items['line_items'] ) ? count( $line_items['line_items'] ) : ''; ?>
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
            <div class="modal fade" id="test_<?php echo esc_html( $order_id ); ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
							<?php
							$i = 0;
							foreach ( $line_items['line_items'] as $key => $value ) {
								echo '<div class="order-item-popup">';
								echo '<p><b>Product Name: </b>' . esc_html( $value['product_name'] ) . '</p>';
								echo '<p><b>Quantiry: </b>' . intval( $value['quantity'] ) . '</p>';
								echo '<p><b>Price: </b>' . esc_html( $value['item_total'] ) . '</p>';
								echo '</div>';
								?>
								<?php
								$i ++;
							}
							?>
                        </div>
                    </div>
                </div>
            </div>
			<?php
		} // end while
	} // end if
	wp_reset_postdata();
}

/*
 * Floating Shelves order table pagination
 */
add_action( 'wp_ajax_floating_shelves_order_table_pagination', 'floating_shelves_order_table_pagination' );
function floating_shelves_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
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
				'compare' => 'NOT LIKE',
			),
			array(
				'key'     => 'product_cat',
				'value'   => 'wood-hoods-product-category',
				'compare' => 'NOT LIKE',
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
			$line_items              = get_post_meta( get_the_ID(), 'line_items', true );
			$is_priority             = get_post_meta( get_the_ID(), 'is_priority', true );
			$damage_item             = get_post_meta( get_the_ID(), 'damage_item', true );
			$hood_replace            = get_post_meta( get_the_ID(), 'hood_replace', true );
			$f_shelf_replace         = get_post_meta( get_the_ID(), 'f_shelf_replace', true );
			$hall_tree_replace       = get_post_meta( get_the_ID(), 'hall_tree_replace', true );
			$no_replace              = get_post_meta( get_the_ID(), 'no_replace', true );
			$bol_link                = home_url() . '/wp-content/uploads/bol/' . $bill_of_landing_id . '.pdf';
			$shipping_file_link      = home_url() . '/wp-content/uploads/bol/shipping_label_' . $bill_of_landing_id . '.pdf';
			$backgroundg_color       = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
			$bol_regenerated         = get_post_meta( get_the_ID(), 'bol_regenerated', true );
			$checked                 = ( 'yes' === $is_priority ) ? 'checked' : '';
			$total_qty = 0;
			foreach ( $line_items['line_items'] as $s_item ) {
				$total_qty += $s_item['quantity'];
			}
			?>

            <tr style="background-color: #4747471a;">
                <td data-title="Order Id">
                    <input type="checkbox" class="bulk_check" value="test" data-orderid="<?php echo $order_id; ?>" data-postid="<?php echo get_the_ID(); ?>"
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
					       ?>"/>
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

                <td data-title="priority">Normal</td>
                <td data-title="Order Source"><?php echo 'UPS'; ?></td>
                <td data-title="Shipping Label"><button class="btn btn-success print_order" data-orderdata="<?php echo $order_id; ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'print_details' ) ); ?>">Print</button></td>
                <td data-title="Self QTY"><?php echo intval($total_qty); ?></td>
                <td data-title="Order Notes">
                    <button class="btn btn-border" type="button" data-toggle="collapse"
                            data-target="#notes-<?php echo $order_id; ?>"
                            aria-expanded="false" aria-controls="notes-<?php echo $order_id; ?>">Item Detail
                    </button>
                </td>
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
										">View</a></li>
						<?php if ( current_user_can( 'administrator' ) ) : ?>
                            <li><a href="#" data-orderid="<?php echo get_the_ID(); ?>" class="hoodslyhub-delete-order"
                                   data-nonce="<?php echo wp_create_nonce( 'hoodslyhub_delete_order_nonce' ); ?>">Delete</a>
                            </li>
						<?php endif ?>
                    </ul>
                </td>
            </tr>
            <tr class="notes-collapse">
                <td colspan="12">
                    <div class="notes-collapse__body collapse" id="notes-<?php echo $order_id; ?>">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 d-flex">
                                <div class="notes-collapse__body-item order_pre_section">

                                    <div class="product_style_info">
										<?php
										$line_items = get_post_meta( get_the_ID(), 'line_items', true );
										//$trim = explode(' ', trim($line_items['line_items'] ))[0];
										$customer_note = get_post_meta( get_the_ID(), 'customer_note', true );
										foreach ( $line_items['line_items'] as $key => $s_item ) { ?>
                                            <div class="syle_finish_sec">
                                                <h4 class="style_head">Floating Shelves Overview</h4>
												<?php
												$hoods_color = $s_item['color']['value'];
												echo '<p>Quantity: ' . intval($s_item['quantity']) . '</p>';
												echo '<p>Color: ' . esc_html( $hoods_color ) . '</p>';
												echo '<p>Width: ' . esc_html( isset($s_item['float_width']['value']) ? $s_item['float_width']['value'] : '' ) . '</p>';
												echo '<p>Depth: ' . esc_html( isset($s_item['float_depth']['value']) ? $s_item['float_depth']['value'] : '' ) . '</p>';
												echo '<p>Thickness: ' . esc_html( isset($s_item['float_thick']['value']) ? $s_item['float_thick']['value'] : '' ) . '</p>';
												?>
                                            </div>
											<?php
										}
										?>
                                    </div>
                                    <div class="product_style_info">
                                        <div class="syle_finish_sec">
                                            <h4 class="style_head">Hoodsly Notes</h4>
                                            <p><?php echo $customer_note; ?></p>
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
	} // end if
	wp_reset_postdata();
}

/*
 * Completed Floating Shelves order table pagination
 */
add_action( 'wp_ajax_completed_floating_shelves_order_table_pagination', 'completed_floating_shelves_order_table_pagination' );
function completed_floating_shelves_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
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
				'compare' => 'NOT LIKE',
			),
			array(
				'key'     => 'product_cat',
				'value'   => 'wood-hoods-product-category',
				'compare' => 'NOT LIKE',
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
			$line_items              = get_post_meta( get_the_ID(), 'line_items', true );
			$is_priority             = get_post_meta( get_the_ID(), 'is_priority', true );
			$damage_item             = get_post_meta( get_the_ID(), 'damage_item', true );
			$hood_replace            = get_post_meta( get_the_ID(), 'hood_replace', true );
			$f_shelf_replace         = get_post_meta( get_the_ID(), 'f_shelf_replace', true );
			$hall_tree_replace       = get_post_meta( get_the_ID(), 'hall_tree_replace', true );
			$no_replace              = get_post_meta( get_the_ID(), 'no_replace', true );
			$bol_link                = home_url() . '/wp-content/uploads/bol/' . $bill_of_landing_id . '.pdf';
			$shipping_file_link      = home_url() . '/wp-content/uploads/bol/shipping_label_' . $bill_of_landing_id . '.pdf';
			$backgroundg_color       = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
			$bol_regenerated         = get_post_meta( get_the_ID(), 'bol_regenerated', true );
			$checked                 = ( 'yes' === $is_priority ) ? 'checked' : '';
			$total_qty = 0;
			foreach ( $line_items['line_items'] as $s_item ) {
				$total_qty += $s_item['quantity'];
			}
			?>

            <tr style="background-color: #4747471a;">
                <td data-title="Order Id">
                    <input type="checkbox" class="bulk_check" value="test" data-orderid="<?php echo $order_id; ?>" data-postid="<?php echo get_the_ID(); ?>"
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
					       ?>"/>
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

                <td data-title="priority">Normal</td>
                <td data-title="Order Source"><?php echo 'UPS'; ?></td>
                <td data-title="Shipping Label"><button class="btn btn-success print_order" data-orderdata="<?php echo $order_id; ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'print_details' ) ); ?>">Print</button></td>
                <td data-title="Self QTY"><?php echo intval($total_qty); ?></td>
                <td data-title="Order Notes">
                    <button class="btn btn-border" type="button" data-toggle="collapse"
                            data-target="#notes-<?php echo $order_id; ?>"
                            aria-expanded="false" aria-controls="notes-<?php echo $order_id; ?>">Item Detail
                    </button>
                </td>
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
										">View</a></li>
						<?php if ( current_user_can( 'administrator' ) ) : ?>
                            <li><a href="#" data-orderid="<?php echo get_the_ID(); ?>" class="hoodslyhub-delete-order"
                                   data-nonce="<?php echo wp_create_nonce( 'hoodslyhub_delete_order_nonce' ); ?>">Delete</a>
                            </li>
						<?php endif ?>
                    </ul>
                </td>
            </tr>
            <tr class="notes-collapse">
                <td colspan="12">
                    <div class="notes-collapse__body collapse" id="notes-<?php echo $order_id; ?>">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 d-flex">
                                <div class="notes-collapse__body-item order_pre_section">

                                    <div class="product_style_info">
										<?php
										$line_items = get_post_meta( get_the_ID(), 'line_items', true );
										//$trim = explode(' ', trim($line_items['line_items'] ))[0];
										$customer_note = get_post_meta( get_the_ID(), 'customer_note', true );
										foreach ( $line_items['line_items'] as $key => $s_item ) { ?>
                                            <div class="syle_finish_sec">
                                                <h4 class="style_head">Floating Shelves Overview</h4>
												<?php
												$hoods_color = $s_item['color']['value'];
												echo '<p>Quantity: ' . intval($s_item['quantity']) . '</p>';
												echo '<p>Color: ' . esc_html( $hoods_color ) . '</p>';
												echo '<p>Width: ' . esc_html( isset($s_item['float_width']['value']) ? $s_item['float_width']['value'] : '' ) . '</p>';
												echo '<p>Depth: ' . esc_html( isset($s_item['float_depth']['value']) ? $s_item['float_depth']['value'] : '' ) . '</p>';
												echo '<p>Thickness: ' . esc_html( isset($s_item['float_thick']['value']) ? $s_item['float_thick']['value'] : '' ) . '</p>';
												?>
                                            </div>
											<?php
										}
										?>
                                    </div>
                                    <div class="product_style_info">
                                        <div class="syle_finish_sec">
                                            <h4 class="style_head">Hoodsly Notes</h4>
                                            <p><?php echo $customer_note; ?></p>
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
	} // end if
	wp_reset_postdata();
}