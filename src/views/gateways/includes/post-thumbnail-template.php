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

