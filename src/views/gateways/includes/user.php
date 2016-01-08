<?php

function get_user_option( $option, $user = 0, $deprecated = '' ) {
	global $wpdb;

	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '3.0' );

	if ( empty( $user ) )
		$user = Yii::$app->user->id;

	if ( ! $user = Yii::$app->user->identity )
		return false;

	// $prefix = $wpdb->get_blog_prefix();
	// if ( $user->has_prop( $prefix . $option ) ) // Blog specific
	// 	$result = $user->get( $prefix . $option );
	// elseif ( $user->has_prop( $option ) ) // User specific and cross-blog
	// 	$result = $user->get( $option );
	// else
		$result = false;

	return apply_filters( "get_user_option_{$option}", $result, $option, $user );
}

function get_current_user_id() {
	if ( ! function_exists( 'wp_get_current_user' ) )
		return 0;
	$user = wp_get_current_user();
	return ( isset( $user->ID ) ? (int) $user->ID : 0 );
}

function setup_userdata($for_user_id = '') {
	global $user_login, $userdata, $user_level, $user_ID, $user_email, $user_url, $user_identity;

	if ( '' == $for_user_id )
		$for_user_id = get_current_user_id();
	$user = get_userdata( $for_user_id );

	if ( ! $user ) {
		$user_ID = 0;
		$user_level = 0;
		$userdata = null;
		$user_login = $user_email = $user_url = $user_identity = '';
		return;
	}

	$user_ID    = (int) $user->ID;
	$user_level = (int) $user->user_level;
	$userdata   = $user;
	$user_login = $user->user_login;
	$user_email = $user->user_email;
	$user_url   = $user->user_url;
	$user_identity = $user->display_name;
}

