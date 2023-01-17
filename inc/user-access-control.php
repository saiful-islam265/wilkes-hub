<?php

class WilkesHubUserAccessControl {

	private $wp_login_php;

	public function __construct() {

		//add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ), 9999 );
		add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );

		add_filter( 'site_url', array( $this, 'site_url' ), 10, 4 );

		add_filter( 'login_url', array( $this, 'login_url' ), 10, 3 );
		add_action( 'login_redirect', array( $this, 'login_redirect' ), 10, 3 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

		add_action( 'template_redirect', array( $this, 'force_login' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'login_logo' ) );

		//add_action( 'admin_init', array( $this, 'remove_admin_access' ) );
		add_filter( 'login_headerurl', array( $this, 'alter_login_logo_url' ), 10, 3 );

	}

	public function remove_admin_access() {
		 //wp_redirect( home_url( '/local-deliveries' ) );
		$role                = get_role( 'driver' );
		$driver_manager_role = get_role( 'driver_manager' );
		$role->remove_cap( 'read' );
		$driver_manager_role->remove_cap( 'read' );
	}

	public function login_logo() { ?>
		<style type="text/css">
			#login h1 a, .login h1 a {
				background-image: url(<?php bloginfo( 'template_directory' ); ?>/assets/images/logo.svg);
				height: 60px;
				width: 250px;
				background-size: contain;
				background-repeat: no-repeat;
				background-color: transparent;
				margin-top: 30px;
			}

			.login #backtoblog, .login #nav {
				padding: 5px 10px !important;
				background: #fff;
				margin: 0 !important;
			}
			.login form{
				border: none !important;
				box-shadow: none !important;
			}
			.login #login_error, .login .message, .login .success {
				border-left: 4px solid #AA4098 !important;
			}
			.wp-core-ui .button-primary {
				background: #AA4098 !important;
				border-color: #AA4098 !important;
			}

			#login {
				border-radius: 18px;
				margin: 150px auto !important;
				padding: 15px 20px 20px !important;
				background-color: #FFFFFF !important;
				border: 1px solid #707070 !important;
			}
			#login #backtoblog{
				display: none;
			}

		</style>
		<?php
	}
	function alter_login_logo_url() {
		return home_url();
	}
	public function force_login() {

		if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			return;
		}
		if ( ! is_user_logged_in() ) {
			$url  = isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
			$url .= '://' . $_SERVER['HTTP_HOST'];
			if ( strpos( $_SERVER['HTTP_HOST'], ':' ) === false ) {
				$url .= in_array( $_SERVER['SERVER_PORT'], array( '80', '443' ), true ) ? '' : ':' . $_SERVER['SERVER_PORT'];
			}
			$url .= $_SERVER['REQUEST_URI'];

			$user              = wp_get_current_user();
			$not_allowed_roles = array( 'driver', 'driver_manager' );
			if ( array_intersect( $not_allowed_roles, $user->roles ) ) {
				$redirect_url = home_url( '/local-deliveries' );
			} else {
				$redirect_url = $url;
			}
			nocache_headers();
			wp_safe_redirect( $this->login_url( $redirect_url ), 302 );
			exit;
		}
	}


	public function site_url( $url, $path, $scheme, $blog_id ) {
		return $this->filter_wp_login_php( $url, $scheme );
	}

	public function filter_wp_login_php( $url, $scheme = null ) {

		if ( strpos( $url, 'wp-login.php?action=postpass' ) !== false ) {
			return $url;
		}

		if ( strpos( $url, 'wp-login.php' ) !== false ) {

			if ( is_ssl() ) {

				$scheme = 'https';

			}

			$args = explode( '?', $url );

			if ( isset( $args[1] ) ) {

				parse_str( $args[1], $args );

				if ( isset( $args['login'] ) ) {
					$args['login'] = rawurlencode( $args['login'] );
				}

				$url = add_query_arg( $args, $this->new_login_url( $scheme ) );

			} else {

				$url = $this->new_login_url( $scheme );

			}
		}

		return $url;

	}

	function login_redirect( $redirect_to, $request, $user ) {
		if ( isset( $user->roles ) && is_array( $user->roles ) ) {
			if ( in_array( 'driver', $user->roles, true ) || in_array( 'driver_manager', $user->roles, true ) ) {
				$redirect_to = home_url( '/local-deliveries' );
			}
		} else {
			$redirect_to = home_url( '/orders' );
		}
		return $redirect_to;
	}
	private function use_trailing_slashes() {

		return ( '/' === substr( get_option( 'permalink_structure' ), - 1, 1 ) );

	}

	private function user_trailingslashit( $string ) {

		return $this->use_trailing_slashes() ? trailingslashit( $string ) : untrailingslashit( $string );

	}

	private function wp_template_loader() {

		global $pagenow;

		$pagenow = 'index.php';

		if ( ! defined( 'WP_USE_THEMES' ) ) {

			define( 'WP_USE_THEMES', true );

		}

		wp();

		if ( $_SERVER['REQUEST_URI'] === $this->user_trailingslashit( str_repeat( '-/', 10 ) ) ) {

			$_SERVER['REQUEST_URI'] = $this->user_trailingslashit( '/wp-login-php/' );

		}

		require_once ABSPATH . WPINC . '/template-loader.php';

		die;

	}

	private function new_login_slug() {
		$slug = 'wilkes-login';
		return $slug;
	}


	public function new_login_url( $scheme = null ) {

		if ( get_option( 'permalink_structure' ) ) {

			return $this->user_trailingslashit( home_url( '/', $scheme ) . $this->new_login_slug() );

		} else {

			return home_url( '/', $scheme ) . '?' . $this->new_login_slug();

		}

	}


	public function after_setup_theme() {

		global $pagenow;

		if ( ! is_multisite()
			 && ( strpos( $_SERVER['REQUEST_URI'], 'wp-signup' ) !== false
				  || strpos( $_SERVER['REQUEST_URI'], 'wp-activate' ) !== false ) && apply_filters( 'wps_hide_login_signup_enable', false ) === false ) {

			wp_die( __( 'This feature is not enabled.', 'wpserveur-hide-login' ) );

		}

		$request = parse_url( $_SERVER['REQUEST_URI'] );

		if ( isset( $request['query'] ) && strpos( $request['query'], 'action=confirmaction' ) !== false ) {
			@require_once ABSPATH . 'wp-login.php';

			$pagenow = 'index.php';
		} elseif ( ( strpos( rawurldecode( $_SERVER['REQUEST_URI'] ), 'wp-login.php' ) !== false
					 || ( isset( $request['path'] ) && untrailingslashit( $request['path'] ) === site_url( 'wp-login', 'relative' ) ) )
				   && ! is_admin() ) {

			$this->wp_login_php = true;

			$_SERVER['REQUEST_URI'] = $this->user_trailingslashit( '/' . str_repeat( '-/', 10 ) );

			$pagenow = 'index.php';

		} elseif ( ( isset( $request['path'] ) && untrailingslashit( $request['path'] ) === home_url( $this->new_login_slug(), 'relative' ) )
				   || ( ! get_option( 'permalink_structure' )
						&& isset( $_GET[ $this->new_login_slug() ] )
						&& empty( $_GET[ $this->new_login_slug() ] ) ) ) {

			$pagenow = 'wp-login.php';

		} elseif ( ( strpos( rawurldecode( $_SERVER['REQUEST_URI'] ), 'wp-register.php' ) !== false
					 || ( isset( $request['path'] ) && untrailingslashit( $request['path'] ) === site_url( 'wp-register', 'relative' ) ) )
				   && ! is_admin() ) {

			$this->wp_login_php = true;

			$_SERVER['REQUEST_URI'] = $this->user_trailingslashit( '/' . str_repeat( '-/', 10 ) );

			$pagenow = 'index.php';
		}

	}

	public function wp_loaded() {

		global $pagenow;

		$request = parse_url( $_SERVER['REQUEST_URI'] );

		if ( ! isset( $_POST['post_password'] ) ) {

			if ( is_admin() && ! is_user_logged_in() && ! defined( 'DOING_AJAX' ) && $pagenow !== 'admin-post.php' && ( isset( $_GET ) && empty( $_GET['adminhash'] ) && $request['path'] !== '/wp-admin/options.php' ) ) {
				wp_safe_redirect( $this->new_login_url() );
				die();
			}

			if ( $pagenow === 'wp-login.php'
				 && $request['path'] !== $this->user_trailingslashit( $request['path'] )
				 && get_option( 'permalink_structure' ) ) {

				wp_safe_redirect(
					$this->user_trailingslashit( $this->new_login_url() )
					. ( ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : '' )
				);

				die;

			} elseif ( $this->wp_login_php ) {

				if ( ( $referer = wp_get_referer() )
					 && strpos( $referer, 'wp-activate.php' ) !== false
					 && ( $referer = wp_parse_url( $referer ) )
					 && ! empty( $referer['query'] ) ) {

					parse_str( $referer['query'], $referer );

					if ( ! empty( $referer['key'] )
						 && ( $result = wpmu_activate_signup( $referer['key'] ) )
						 && is_wp_error( $result )
						 && ( $result->get_error_code() === 'already_active'
							  || $result->get_error_code() === 'blog_taken' ) ) {

						wp_safe_redirect(
							$this->new_login_url()
							. ( ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : '' )
						);

						die;

					}
				}

				$this->wp_template_loader();

			} elseif ( 'wp-login.php' === $pagenow ) {
				global $error, $interim_login, $action, $user_login;

				if ( is_user_logged_in() && ! isset( $_REQUEST['action'] ) ) {
					wp_safe_redirect( admin_url() );
					die();
				}

				@require_once ABSPATH . 'wp-login.php';

				die;

			}
		}

	}

	public function login_url( $redirect = '' ) {
		$login_page = home_url( '/wilkes-login/' );
		if ( ! empty( $redirect ) ) {
			$login_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $login_page );
		}
		return $login_url;
	}

	public function enqueue() {

		$user              = wp_get_current_user();
		$not_allowed_roles = array( 'driver', 'driver_manager' );
		if ( array_intersect( $not_allowed_roles, $user->roles ) ) {
			?>
			<style type="text/css">
				#wpadminbar .quicklinks ul#wp-admin-bar-root-default{
					display: none;
				}
			</style>
			<?php
		}
	}
}

new WilkesHubUserAccessControl();
