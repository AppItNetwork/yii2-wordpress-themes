<?php

function post_type_supports( $post_type, $feature ) {
	global $_wp_post_type_features;

	return ( isset( $_wp_post_type_features[$post_type][$feature] ) );
}

function is_sticky( $post_id = 0 ) {
	$post_id = absint( $post_id );

	if ( ! $post_id )
		$post_id = get_the_ID();

	$stickies = get_option( 'sticky_posts' );

	if ( ! is_array( $stickies ) )
		return false;

	if ( in_array( $post_id, $stickies ) )
		return true;

	return false;
}

function get_post_meta( $post_id, $key = '', $single = false ) {
	return get_metadata('post', $post_id, $key, $single);
}

