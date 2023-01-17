<?php
//  Assets Loader

function hoodsly_hub_assets_loader() {

	/*** Enqueue styles. */
	wp_enqueue_style( 'adobe-typekit', 'https://use.typekit.net/hes4fbb.css', array(), false, 'all' );
	wp_enqueue_style( 'plugins', get_template_directory_uri() . '/assets/css/plugins.css', array(), date( "ymd-Gis", filemtime( get_template_directory() . '/assets/css/plugins.css' ) ), 'all' );
	wp_enqueue_style( 'hoodslyhub-style', get_stylesheet_uri(), array(), date( "ymd-Gis", filemtime( get_stylesheet_directory() ) ) );
	wp_enqueue_style( 'jquery-ui-wrh-css', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css', array(), date( "ymd-Gis", filemtime( get_stylesheet_directory() ) ) );

	/*** Enqueue scripts. */
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-wrh-js', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array('jquery'), time(), true );
	wp_enqueue_script( 'plugins', get_template_directory_uri() . '/assets/js/plugins.js', array(), date( "ymd-Gis", filemtime( get_template_directory() . '/assets/js/plugins.js' ) ), true );
	wp_enqueue_script( 'scripts', get_template_directory_uri() . '/assets/js/scripts.js', array(), date( "ymd-Gis", filemtime( get_template_directory() . '/assets/js/scripts.js' ) ), true );
	wp_localize_script( 'scripts', 'ajaxRequest', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

add_action( 'wp_enqueue_scripts', 'hoodsly_hub_assets_loader' );

// Enqueue google font
function hoodslyhub_load_fonts() {
	?>
    <!-– Googele Fonts –->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap"
          rel="stylesheet">
    <!-– End of Google Fonts –->
	<?php
}

add_action( 'wp_head', 'hoodslyhub_load_fonts' );

/*** Register and enqueue a custom stylesheet in the WordPress admin. */
function admin_scripts() {
	wp_enqueue_style( 
		'icon-fonts', 
		get_template_directory_uri() . '/assets/css/hoodslyhub-font-icons.css', 
		array(), 
		false, 
		'all'
	);
	// wp_enqueue_style(
	// 	'wilkes-admin-style',
	// 	get_template_directory_uri() . '/assets/css/admin.css',
	// 	array(),
	// 	gmdate(
	// 		'ymd-Gis',
	// 		filemtime(
	// 			get_template_directory() . '/assets/css/admin.css'
	// 		)
	// 	),
	// 	'all'
	// );
	// wp_enqueue_script(
	// 	'wilkes-admin-script',
	// 	get_template_directory_uri() . '/assets/js/admin.js',
	// 	array('jquery'),
	// 	gmdate(
	// 		'ymd-Gis',
	// 		filemtime(
	// 			get_template_directory() . '/assets/js/admin.js'
	// 		)
	// 	),
	// 	true
	// );

	// wp_localize_script('wilkes-admin-script', 'wilkeslocalizeData', [
	// 	'ajaxURL' => esc_url(admin_url('admin-ajax.php'))
	// ]);
	

}

add_action( 'admin_enqueue_scripts', 'admin_scripts' );