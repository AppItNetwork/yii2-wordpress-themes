<?php

function wp_get_nav_menu_object( $menu ) {
	$menu_obj = false;

	if ( is_object( $menu ) ) {
		$menu_obj = $menu;
	}

	if ( $menu && ! $menu_obj ) {
		$menu_obj = get_term( $menu, 'nav_menu' );

		if ( ! $menu_obj ) {
			$menu_obj = get_term_by( 'slug', $menu, 'nav_menu' );
		}

		if ( ! $menu_obj ) {
			$menu_obj = get_term_by( 'name', $menu, 'nav_menu' );
		}
	}

	if ( ! $menu_obj || is_wp_error( $menu_obj ) ) {
		$menu_obj = false;
	}

	return apply_filters( 'wp_get_nav_menu_object', $menu_obj, $menu );
}

function get_nav_menu_locations() {
	$locations = get_theme_mod( 'nav_menu_locations' );
	return ( is_array( $locations ) ) ? $locations : array();
}

function has_nav_menu( $location ) {
	$has_nav_menu = false;

	$registered_nav_menus = get_registered_nav_menus();
	if ( isset( $registered_nav_menus[ $location ] ) ) {
		$locations = get_nav_menu_locations();
		$has_nav_menu = ! empty( $locations[ $location ] );
	}

	return apply_filters( 'has_nav_menu', $has_nav_menu, $location );
}

function get_registered_nav_menus() {
	global $_wp_registered_nav_menus;
	if ( isset( $_wp_registered_nav_menus ) )
		return $_wp_registered_nav_menus;
	return array();
}

function register_nav_menu( $location, $description ) {
	register_nav_menus( array( $location => $description ) );
}

function register_nav_menus( $locations = array() ) {
	global $_wp_registered_nav_menus;

	add_theme_support( 'menus' );

	$_wp_registered_nav_menus = array_merge( (array) $_wp_registered_nav_menus, $locations );
}

