<?php

function has_post_thumbnail( $post = null ) {
	return (bool) get_post_thumbnail_id( $post );
}

function get_post_thumbnail_id( $post = null ) {
	$post = get_post();
	// $post = Yii::$app->wpthemes->post;
	if ( ! $post ) {
		return '';
	}
	return get_post_meta( $post->ID, '_thumbnail_id', true );
}

function the_post_thumbnail( $size = 'post-thumbnail', $attr = '' ) {
	echo get_the_post_thumbnail( null, $size, $attr );
}

function get_the_post_thumbnail( $post = null, $size = 'post-thumbnail', $attr = '' ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	$post_thumbnail_id = get_post_thumbnail_id( $post );

	$size = apply_filters( 'post_thumbnail_size', $size );

	if ( $post_thumbnail_id ) {

		do_action( 'begin_fetch_post_thumbnail_html', $post->ID, $post_thumbnail_id, $size );
		if ( in_the_loop() )
			update_post_thumbnail_cache();
		$html = wp_get_attachment_image( $post_thumbnail_id, $size, false, $attr );

		do_action( 'end_fetch_post_thumbnail_html', $post->ID, $post_thumbnail_id, $size );

	} else {
		$html = '';
	}
	return apply_filters( 'post_thumbnail_html', $html, $post->ID, $post_thumbnail_id, $size, $attr );
}

