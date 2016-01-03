<?php

function is_preview() {
	return false;
}

function is_singular( $post_types = '' ) {
	return false;
}

function is_post_type_archive( $post_types = '' ) {
	return false;
}

function is_category( $category = '' ) {
	return false;
}

function is_tag( $tag = '' ) {
	return false;
}

function is_author( $author = '' ) {
	return false;
}

function is_search( ) {
	return false;
}

function is_single( $post = '' ) {
	return false;
}

function is_embed() {
	return false;
}

function is_paged() {
	return false;
}

function is_tax( $taxonomy = '', $term = '' ) {
	return false;
}

function is_archive() {
	return false;
}

function is_404() {
	return false;
}

function is_feed( $feeds = '' ) {
	return false;
}

function is_attachment( $attachment = '' ) {
	return false;
}

function get_query_var( $var, $default = '' ) {
	if ( Yii::$app->request->isGet ) {
		return Yii::$app->request->get( $var );
	}
}

function is_page( $page = '' ) {
	return true;
}

function is_front_page() {
	return true;
}

function is_home() {
	return true;
}

function have_posts() {
	return Yii::$app->wpthemes->have_posts();
	// return false;
}

function the_post() {
// pr('the post');die;	
	Yii::$app->wpthemes->the_post();
	// return false;
}

function setup_postdata( $post ) {
	// global $wp_query;

	// if ( ! empty( $wp_query ) && $wp_query instanceof \appitnetwork\wpthemes\components\WP ) {
		return $Yii::$app->wpthemes->setup_postdata( $post );
	// }

	// return false;
}
