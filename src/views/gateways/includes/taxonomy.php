<?php
use appitnetwork\wpthemes\helpers\WP_Term;

function get_term( $term, $taxonomy = '', $output = OBJECT, $filter = 'raw' ) {
	if ( empty( $term ) ) {
		return new WP_Error( 'invalid_term', __( 'Empty Term' ) );
	}

	if ( $taxonomy && ! taxonomy_exists( $taxonomy ) ) {
		// pr($term);pr($taxonomy);die;
		// return new WP_Error( 'invalid_taxonomy', __( 'Invalid taxonomy' ) );
		$taxonomy = '';
	}

	if ( $term instanceof WP_Term ) {
		$_term = $term;
	} elseif ( is_object( $term ) ) {
		if ( empty( $term->filter ) || 'raw' === $term->filter ) {
			$_term = sanitize_term( $term, $taxonomy, 'raw' );
			$_term = new WP_Term( $_term );
		} else {
			$_term = WP_Term::get_instance( $term->term_id );
		}
	} else {
		$_term = WP_Term::get_instance( $term, $taxonomy );
	}

	if ( is_wp_error( $_term ) ) {
		return $_term;
	} elseif ( ! $_term ) {
		return null;
	}

	$_term = apply_filters( 'get_term', $_term, $taxonomy );

	$_term = apply_filters( "get_$taxonomy", $_term, $taxonomy );

	// Sanitize term, according to the specified filter.
	$_term->filter( $filter );

	if ( $output == ARRAY_A ) {
		return $_term->to_array();
	} elseif ( $output == ARRAY_N ) {
		return array_values( $_term->to_array() );
	}

	return $_term;
}

function taxonomy_exists( $taxonomy ) {
	global $wp_taxonomies;

	return isset( $wp_taxonomies[$taxonomy] );
}

function get_term_by( $field, $value, $taxonomy = '', $output = OBJECT, $filter = 'raw' ) {
	global $wpdb;

	// 'term_taxonomy_id' lookups don't require taxonomy checks.
	if ( 'term_taxonomy_id' !== $field && ! taxonomy_exists( $taxonomy ) ) {
		return false;
	}

	$tax_clause = $wpdb->prepare( "AND tt.taxonomy = %s", $taxonomy );

	if ( 'slug' == $field ) {
		$_field = 't.slug';
		$value = sanitize_title($value);
		if ( empty($value) )
			return false;
	} elseif ( 'name' == $field ) {
		// Assume already escaped
		$value = wp_unslash($value);
		$_field = 't.name';
	} elseif ( 'term_taxonomy_id' == $field ) {
		$value = (int) $value;
		$_field = 'tt.term_taxonomy_id';

		// No `taxonomy` clause when searching by 'term_taxonomy_id'.
		$tax_clause = '';
	} else {
		$term = get_term( (int) $value, $taxonomy, $output, $filter );
		if ( is_wp_error( $term ) || is_null( $term ) ) {
			$term = false;
		}
		return $term;
	}

	$term = $wpdb->get_row( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE $_field = %s $tax_clause LIMIT 1", $value ) );
	if ( ! $term )
		return false;

	// In the case of 'term_taxonomy_id', override the provided `$taxonomy` with whatever we find in the db.
	if ( 'term_taxonomy_id' === $field ) {
		$taxonomy = $term->taxonomy;
	}

	wp_cache_add( $term->term_id, $term, 'terms' );

	return get_term( $term, $taxonomy, $output, $filter );
}

function get_taxonomies( $args = array(), $output = 'names', $operator = 'and' ) {
	global $wp_taxonomies;

	$field = ('names' == $output) ? 'name' : false;

	return wp_filter_object_list($wp_taxonomies, $args, $operator, $field);
}

