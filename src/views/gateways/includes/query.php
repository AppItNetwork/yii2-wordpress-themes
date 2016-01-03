<?php

function get_query_var( $var, $default = '' ) {
	if ( Yii::$app->request->isGet ) {
		return Yii::$app->request->get( $var );
	}
}

function is_preview() {
	return Yii::$app->view->wp_query->is_preview();
}

function is_post_type_archive( $post_types = '' ) {
	return Yii::$app->view->wp_query->is_post_type_archive( $post_types );
}

function is_category( $category = '' ) {
	return Yii::$app->view->wp_query->is_category( $category );
}

function is_tag( $tag = '' ) {
	return Yii::$app->view->wp_query->is_tag( $tag );
}

function is_author( $author = '' ) {
	return Yii::$app->view->wp_query->is_author( $author );
}

function is_robots() {
	return Yii::$app->view->wp_query->is_robots();
}

function is_search( ) {
	return Yii::$app->view->wp_query->is_search();
}

function is_single( $post = '' ) {
	return Yii::$app->view->wp_query->is_single( $post );
}

function is_singular( $post_types = '' ) {
	return Yii::$app->view->wp_query->is_singular( $post_types );
}

function is_time() {
	return Yii::$app->view->wp_query->is_time();
}

function is_trackback() {
	return Yii::$app->view->wp_query->is_trackback();
}

function is_embed() {
	return Yii::$app->view->wp_query->is_embed();
}

function is_tax( $taxonomy = '', $term = '' ) {
	return Yii::$app->view->wp_query->is_tax( $taxonomy, $term );
}

function is_comments_popup() {
	return Yii::$app->view->wp_query->is_comments_popup();
}

function is_comment_feed() {
	return Yii::$app->view->wp_query->is_comment_feed();
}

function is_archive() {
	return Yii::$app->view->wp_query->is_archive();
}

function is_date() {
	return Yii::$app->view->wp_query->is_date();
}

function is_day() {
	return Yii::$app->view->wp_query->is_day();
}

function is_month() {
	return Yii::$app->view->wp_query->is_month();
}

function is_year() {
	return Yii::$app->view->wp_query->is_year();
}

function is_404() {
	return Yii::$app->view->wp_query->is_404();
}

function is_feed( $feeds = '' ) {
	return Yii::$app->view->wp_query->is_feed( $feeds );
}

function is_attachment( $attachment = '' ) {
	return Yii::$app->view->wp_query->is_attachment( $attachment );
}

function is_page( $page = '' ) {
	return Yii::$app->view->wp_query->is_page( $page );
}

function is_paged( ) {
	return Yii::$app->view->wp_query->is_paged();
}

function is_front_page() {
	return Yii::$app->view->wp_query->is_front_page();
}

function is_home() {
	return Yii::$app->view->wp_query->is_home();
}

function have_posts() {
	return Yii::$app->view->wp_query->have_posts();
}

function in_the_loop() {
	return Yii::$app->view->wp_query->in_the_loop;
}

function rewind_posts() {
	Yii::$app->view->wp_query->rewind_posts();
}

function the_post() {
	Yii::$app->view->wp_query->the_post();
}

function have_comments() {
	return Yii::$app->view->wp_query->have_comments();
}

function the_comment() {
	return Yii::$app->view->wp_query->the_comment();
}

function setup_postdata( $post ) {
	if ( ! empty( Yii::$app->view->wp_query ) && Yii::$app->view->wp_query instanceof \appitnetwork\wpthemes\models\WP_Query ) {
		return $Yii::$app->wpthemes->setup_postdata( $post );
	}

	return false;
}
