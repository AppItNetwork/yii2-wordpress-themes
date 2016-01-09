<?php

function get_metadata($meta_type, $object_id, $meta_key = '', $single = false) {
	if ( ! $meta_type || ! is_numeric( $object_id ) ) {
		return false;
	}

	$object_id = absint( $object_id );
	if ( ! $object_id ) {
		return false;
	}

	$check = apply_filters( "get_{$meta_type}_metadata", null, $object_id, $meta_key, $single );
	if ( null !== $check ) {
		if ( $single && is_array( $check ) )
			return $check[0];
		else
			return $check;
	}

	// $meta_cache = wp_cache_get($object_id, $meta_type . '_meta');

	if ( !$meta_cache ) {
		// $meta_cache = update_meta_cache( $meta_type, array( $object_id ) );
		// $meta_cache = $meta_cache[$object_id];
	}

	if ( ! $meta_key ) {
		return $meta_cache;
	}

	if ( isset($meta_cache[$meta_key]) ) {
		if ( $single )
			return maybe_unserialize( $meta_cache[$meta_key][0] );
		else
			return array_map('maybe_unserialize', $meta_cache[$meta_key]);
	}

	if ($single)
		return '';
	else
		return array();
}

function metadata_exists( $meta_type, $object_id, $meta_key ) {
	if ( ! $meta_type || ! is_numeric( $object_id ) ) {
		return false;
	}

	$object_id = absint( $object_id );
	if ( ! $object_id ) {
		return false;
	}

	/** This filter is documented in wp-includes/meta.php */
	$check = apply_filters( "get_{$meta_type}_metadata", null, $object_id, $meta_key, true );
	if ( null !== $check )
		return (bool) $check;

	$meta_cache = wp_cache_get( $object_id, $meta_type . '_meta' );

	if ( !$meta_cache ) {
		$meta_cache = update_meta_cache( $meta_type, array( $object_id ) );
		$meta_cache = $meta_cache[$object_id];
	}

	if ( isset( $meta_cache[ $meta_key ] ) )
		return true;

	return false;
}

