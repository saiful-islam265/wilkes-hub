<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div class="hoodslyhub-user-dashboard">
	<header class="dashboard__header">
		<nav class="navbar navbar-expand">
			<div class="container-fluid">
				<div class="navbar-header">
					<a href="#" class="navbar-toggle">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<a class="navbar-brand" href="<?php echo esc_url( home_url() ); ?>">
						<!--                        <img src="assets/images/logo.svg" class="img-fluid" alt="logo">-->
						<?php
						printf(
							'<img width="157px" src="%s" class="img-fluid" alt="%s">',
							esc_url( get_theme_file_uri( 'assets/images/logo.svg' ) ),
							esc_html( get_bloginfo( 'name' ) )
						);
						?>
					</a>
				</div>
				<?php
					$current_user           = wp_get_current_user();
					$args                   = array(
						'posts_per_page' => -1,
						'post_type'      => 'order_communication',
						'meta_query'     => array(
							array(
								'key'     => 'mentioned_user',
								'value'   => $current_user->user_login,
								'compare' => 'LIKE',
							),
						),
					);
					$no_query               = new WP_Query( $args );
					$notification_count     = get_user_meta( get_current_user_id(), 'notification_count', true );
					$has_notification_class = '';
					if ( $notification_count > 0 ) {
						$has_notification_class = 'show_color';
					}
					?>
				<div class="navbar-collapse" >
					<ul class="navbar-nav ml-auto">
						<li><a href="#" class="icon-paste"></a></li>
						<li><a href="#" class="icon-email-open"></a></li>
						<li class="dropdown">
							<a href="#" role="button" class="icon-notifications save_notification_count <?php echo $has_notification_class; ?>" data-toggle="dropdown"></a>
							<ul class="order_notification_list dropdown-menu notification_scroll" id="order_notification_list">
							<?php
							$order_link = get_template_link( 't_order-details.php' );
							if ( $no_query->have_posts() ) {
								while ( $no_query->have_posts() ) {
									$no_query->the_post();
									$agent_replied = get_post_meta( get_the_ID(), 'agent_replied', true );
									$order_id      = get_post_meta( get_the_ID(), 'order_id', true );
									$post_id       = get_post_meta( get_the_ID(), 'post_id', true );
									$user          = get_userdata( $agent_replied );
									echo '<li><a href="#">' . $user->data->user_login . '</a> mentioned you to following this order <a href="' . esc_url(
										add_query_arg(
											array(
												'post_id'  => $post_id,
												'order_id' => $order_id,
											),
											$order_link
										)
									) . '#comments_history">' . $order_id . '</a> <span class="comment_date"><i>' . get_the_date( 'Y-m-d h:i:sa' ) . '</i></span></li><hr>';
								}
							}
							//update_user_meta(get_current_user_id() ,'notification_count', $no_query->post_count);

							?>
							</ul>
							<?php $notification_count = get_user_meta( get_current_user_id(), 'notification_count', true ); ?>
						</li>
						<span class="badge notification_number"><?php echo $notification_count; ?></span>
					</ul>
				</div>
			</div>
		</nav><!-- /nav -->
	</header><!-- /header -->
	<div class="header-gutter"></div>
	<?php get_template_part( 'template-parts/sidebar', 'menu' ); ?>
