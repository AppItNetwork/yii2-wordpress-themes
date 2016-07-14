<?php

function rest_output_link_wp_head() {
	$api_root = get_rest_url();

	if ( empty( $api_root ) ) {
		return;
	}

	echo "<link rel='https://api.w.org/' href='" . esc_url( $api_root ) . "' />\n";
}

function get_rest_url( $blog_id = null, $path = '/', $scheme = 'rest' ) {
	if ( empty( $path ) ) {
		$path = '/';
	}

	if ( is_multisite() && get_blog_option( $blog_id, 'permalink_structure' ) || get_option( 'permalink_structure' ) ) {
		$url = get_home_url( $blog_id, rest_get_url_prefix(), $scheme );
		$url .= '/' . ltrim( $path, '/' );
	} else {
		$url = trailingslashit( get_home_url( $blog_id, '', $scheme ) );

		$path = '/' . ltrim( $path, '/' );

		$url = add_query_arg( 'rest_route', $path, $url );
	}

	if ( is_ssl() ) {
		// If the current host is the same as the REST URL host, force the REST URL scheme to HTTPS.
		if ( $_SERVER['SERVER_NAME'] === parse_url( get_home_url( $blog_id ), PHP_URL_HOST ) ) {
			$url = set_url_scheme( $url, 'https' );
		}
	}

	return apply_filters( 'rest_url', $url, $path, $blog_id, $scheme );
}

function rest_get_url_prefix() {
	return apply_filters( 'rest_url_prefix', 'wp-json' );
}

function rest_api_init() {
	// rest_api_register_rewrites();

	global $wp;
	$wp->add_query_var( 'rest_route' );
}

function rest_api_loaded() {
	if ( empty( $GLOBALS['wp']->query_vars['rest_route'] ) ) {
		return;
	}

	define( 'REST_REQUEST', true );

	/** @var WP_REST_Server $wp_rest_server */
	global $wp_rest_server;

	$wp_rest_server_class = apply_filters( 'wp_rest_server_class', 'WP_REST_Server' );
	$wp_rest_server = new $wp_rest_server_class;

	do_action( 'rest_api_init', $wp_rest_server );

	// Fire off the request.
	$wp_rest_server->serve_request( $GLOBALS['wp']->query_vars['rest_route'] );

	// We're done.
	die();
}

function rest_url( $path = '', $scheme = 'json' ) {
	return get_rest_url( null, $path, $scheme );
}

