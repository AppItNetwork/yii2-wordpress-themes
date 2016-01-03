<?php

function is_user_logged_in() {
	$user = Yii::$app->user;

	return $user->isGuest;
}

function wp_get_current_user() {
	$user = Yii::$app->user->identity;

	// get_currentuserinfo();

	return $user;
}

// function get_currentuserinfo() {
// 	global $current_user;

// 	if ( ! empty( $current_user ) ) {
// 		if ( $current_user instanceof WP_User )
// 			return;

// 		// Upgrade stdClass to WP_User
// 		if ( is_object( $current_user ) && isset( $current_user->ID ) ) {
// 			$cur_id = $current_user->ID;
// 			$current_user = null;
// 			wp_set_current_user( $cur_id );
// 			return;
// 		}

// 		// $current_user has a junk value. Force to WP_User with ID 0.
// 		$current_user = null;
// 		wp_set_current_user( 0 );
// 		return false;
// 	}

// 	if ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST ) {
// 		wp_set_current_user( 0 );
// 		return false;
// 	}

// 	$user_id = apply_filters( 'determine_current_user', false );
// 	if ( ! $user_id ) {
// 		wp_set_current_user( 0 );
// 		return false;
// 	}

// 	wp_set_current_user( $user_id );
// }
