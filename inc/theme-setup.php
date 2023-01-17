<?php

if ( ! function_exists( 'hoodsluhub_setup' ) ) {

	function hoodsluhub_setup() {
		/** Make theme available for translation. */
		load_theme_textdomain( 'toss', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'navigation-widgets',
			)
		);


		/** This theme uses wp_nav_menu() in one location. */
		register_nav_menus( array(
			'sidebarMenu' => esc_html__( 'Sidebar menu', 'hoodslyhub' ),
		) );

	}

	if ( ! file_exists( get_template_directory() . '/inc/wp_bootstrap_navwalker.php' ) ) {
		// File does not exist... return an error.
		return new WP_Error( 'class-wp-bootstrap-navwalker-missing', __( 'It appears the class-wp-bootstrap-navwalker.php file may be missing.', 'hoodslyhub' ) );
	} else {
		// File exists... require it.
		require get_template_directory() . '/inc/wp_bootstrap_navwalker.php';
	}
}
add_action( 'after_setup_theme', 'hoodsluhub_setup' );