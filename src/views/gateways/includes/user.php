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

