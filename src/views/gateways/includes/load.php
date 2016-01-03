<?php

function is_admin() {
	// if ( isset( $GLOBALS['current_screen'] ) )
	// 	return $GLOBALS['current_screen']->in_admin();
	// elseif ( defined( 'WP_ADMIN' ) )
	// 	return WP_ADMIN;

	return false;
}

function get_current_blog_id() {
	global $blog_id;
	return absint($blog_id);
}

function is_multisite() {
	// if ( defined( 'MULTISITE' ) )
	// 	return MULTISITE;

	// if ( defined( 'SUBDOMAIN_INSTALL' ) || defined( 'VHOST' ) || defined( 'SUNRISE' ) )
	// 	return true;

	return false;
}

function wp_installing( $is_installing = null ) {
	return false;	
}
