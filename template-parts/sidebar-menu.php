<div class="dashboard__vertical-navbar" id="simplebar">
	<nav class="vertical-navbar">
		<?php wp_nav_menu(
			array(
				'depth'          => 2,
				'menu_id'        => '',
				'container'      => false,
				'theme_location' => 'sidebarMenu',
				'menu'           => 'Sidebar Menu',
				'menu_class'     => 'navbar-nav',
				'fallback_cb'    => 'wp_bootstrap_navwalker::fallback',
				'walker'         => new wp_bootstrap_navwalker(),
			)
		); ?>

		<div href="#" class="user-meta mt-auto">
			<figure class="media">
				<?php printf( '<img src="%s" alt="%s">', esc_url( get_theme_file_uri( 'assets/images/user.jpg' ) ), get_bloginfo( 'name' ) ); ?>
			</figure>
			<div class="text">
				<?php
				global $current_user;
				wp_get_current_user();
				?>
				<?php
				if ( is_user_logged_in() ) {
					?>
					<div class="name"><?php echo esc_html( $current_user->display_name ); ?></div>
					<?php
					$user = get_userdata( $current_user->ID );
					// Get all the user roles as an array.
					$user_roles = $user->roles;
					// Check if the role you're interested in, is present in the array.
					?>
					<div class="user-id">User (<?php echo $current_user->display_name; ?>) ID #<?php echo intval( $current_user->ID ); ?></div>

					<?php
				} else {
					wp_loginout(); }
				?>

				<div>
					<a class="user-id" href="<?php echo esc_url( wp_logout_url() ); ?>">
						<?php echo esc_html__( 'Logout', 'hoodslyhub' ); ?>
					</a>
				</div>
			</div>
		</div>
	</nav>
</div>
