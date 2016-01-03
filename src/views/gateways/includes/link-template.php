<?php

function site_url( $path = '', $scheme = null ) {
	return get_site_url( null, $path, $scheme );
}

function get_site_url( $blog_id = null, $path = '', $scheme = null ) {
	if ( empty( $blog_id ) ) {
		$url = get_option( 'siteurl' );
	} else {
		switch_to_blog( $blog_id );
		$url = get_option( 'siteurl' );
		restore_current_blog();
	}

	$url = set_url_scheme( $url, $scheme );

	if ( $path && is_string( $path ) )
		$url .= '/' . ltrim( $path, '/' );

	return apply_filters( 'site_url', $url, $path, $scheme, $blog_id );
}

function set_url_scheme( $url, $scheme = null ) {
	$orig_scheme = $scheme;

	if ( ! $scheme ) {
		$scheme = is_ssl() ? 'https' : 'http';
	} elseif ( $scheme === 'admin' || $scheme === 'login' || $scheme === 'login_post' || $scheme === 'rpc' ) {
		$scheme = is_ssl() || force_ssl_admin() ? 'https' : 'http';
	} elseif ( $scheme !== 'http' && $scheme !== 'https' && $scheme !== 'relative' ) {
		$scheme = is_ssl() ? 'https' : 'http';
	}

	$url = trim( $url );
	if ( substr( $url, 0, 2 ) === '//' )
		$url = 'http:' . $url;

	if ( 'relative' == $scheme ) {
		$url = ltrim( preg_replace( '#^\w+://[^/]*#', '', $url ) );
		if ( $url !== '' && $url[0] === '/' )
			$url = '/' . ltrim($url , "/ \t\n\r\0\x0B" );
	} else {
		$url = preg_replace( '#^\w+://#', $scheme . '://', $url );
	}

	return apply_filters( 'set_url_scheme', $url, $scheme, $orig_scheme );
}

function content_url($path = '') {
	$url = set_url_scheme( WP_CONTENT_URL );

	if ( $path && is_string( $path ) )
		$url .= '/' . ltrim($path, '/');

	return apply_filters( 'content_url', $url, $path);
}

function home_url( $path = '', $scheme = null ) {
	return get_home_url( null, $path, $scheme );
}

function get_home_url( $blog_id = null, $path = '', $scheme = null ) {
	global $pagenow;

	$orig_scheme = $scheme;

	if ( empty( $blog_id ) || !is_multisite() ) {
		$url = get_option( 'home' );
	} else {
		switch_to_blog( $blog_id );
		$url = get_option( 'home' );
		restore_current_blog();
	}

	if ( ! in_array( $scheme, array( 'http', 'https', 'relative' ) ) ) {
		if ( is_ssl() && ! is_admin() && 'wp-login.php' !== $pagenow )
			$scheme = 'https';
		else
			$scheme = parse_url( $url, PHP_URL_SCHEME );
	}

	$url = set_url_scheme( $url, $scheme );

	if ( $path && is_string( $path ) )
		$url .= '/' . ltrim( $path, '/' );

	return apply_filters( 'home_url', $url, $path, $orig_scheme, $blog_id );
}

function includes_url( $path = '', $scheme = null ) {
	$url = site_url( Yii::$app->wpthemes->getAssetUrl() . '/', $scheme );

	if ( $path && is_string( $path ) )
		$url .= ltrim($path, '/');

	return apply_filters( 'includes_url', $url, $path );
}

function adjacent_posts_rel_link_wp_head() {
	if ( ! is_single() || is_attachment() ) {
		return;
	}
	adjacent_posts_rel_link();
}

function rel_canonical() {
	if ( ! is_singular() ) {
		return;
	}

	if ( ! $id = get_queried_object_id() ) {
		return;
	}

	$url = get_permalink( $id );

	$page = get_query_var( 'page' );
	if ( $page ) {
		$url = trailingslashit( $url ) . user_trailingslashit( $page, 'single_paged' );
	}

	$cpage = get_query_var( 'cpage' );
	if ( $cpage ) {
		$url = get_comments_pagenum_link( $cpage );
	}
	echo '<link rel="canonical" href="' . esc_url( $url ) . "\" />\n";
}

function wp_shortlink_wp_head() {
	$shortlink = wp_get_shortlink( 0, 'query' );

	if ( empty( $shortlink ) )
		return;

	echo "<link rel='shortlink' href='" . esc_url( $shortlink ) . "' />\n";
}

function wp_get_shortlink($id = 0, $context = 'post', $allow_slugs = true) {
	$shortlink = apply_filters( 'pre_get_shortlink', false, $id, $context, $allow_slugs );

	if ( false !== $shortlink ) {
		return $shortlink;
	}

	$post_id = 0;
	if ( 'query' == $context && is_singular() ) {
		$post_id = get_queried_object_id();
		$post = get_post( $post_id );
	} elseif ( 'post' == $context ) {
		$post = get_post( $id );
		if ( ! empty( $post->ID ) )
			$post_id = $post->ID;
	}

	$shortlink = '';

	// Return p= link for all public post types.
	if ( ! empty( $post_id ) ) {
		$post_type = get_post_type_object( $post->post_type );

		if ( 'page' === $post->post_type && $post->ID == get_option( 'page_on_front' ) && 'page' == get_option( 'show_on_front' ) ) {
			$shortlink = home_url( '/' );
		} elseif ( $post_type->public ) {
			$shortlink = home_url( '?p=' . $post_id );
		}
	}

	return apply_filters( 'get_shortlink', $shortlink, $id, $context, $allow_slugs );
}

function edit_post_link( $text = null, $before = '', $after = '', $id = 0, $class = 'post-edit-link' ) {
	// if ( ! $post = Yii::$app->wpthemes->post ) {
		return;
	// }

	// if ( ! $url = get_edit_post_link( $post->ID ) ) {
	// 	return;
	// }

	// if ( null === $text ) {
	// 	$text = __( 'Edit This' );
	// }

	// $link = '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '">' . $text . '</a>';

	// echo $before . apply_filters( 'edit_post_link', $link, $post->ID, $text ) . $after;
}

