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

