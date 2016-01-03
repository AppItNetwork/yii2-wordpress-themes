<?php

function current_user_can( $capability ) {
	$current_user = wp_get_current_user();

	if ( empty( $current_user ) )
		return false;

	$args = array_slice( func_get_args(), 1 );
	$args = array_merge( array( $capability ), $args );

	return call_user_func_array( array( $current_user, 'has_cap' ), $args );
}

