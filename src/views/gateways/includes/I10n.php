<?php

function get_locale() {
	return Yii::$app->formatter->locale;
}

function _e( $text, $domain = 'default' ) {
	echo translate( $text, $domain );
}

function __( $text, $domain = 'default' ) {
	return translate( $text, $domain );
}

function translate( $text, $domain = 'default' ) {
	// $translations = Yii::t( $domain, $text );
	$translations = Yii::t( 'app', $text );
	return $translations;
}

function _x( $text, $context, $domain = 'default' ) {
	return translate_with_gettext_context( $text, $context, $domain );
}

function translate_with_gettext_context( $text, $context, $domain = 'default' ) {
	// $translations = get_translations_for_domain( $domain );
	// $translations = $translations->translate( $text, $context );
	$translations = translate( $text, $context );

	return apply_filters( 'gettext_with_context', $translations, $text, $context, $domain );
}

function esc_attr_x( $text, $context, $domain = 'default' ) {
	return esc_attr( translate_with_gettext_context( $text, $context, $domain ) );
}

function esc_attr_e( $text, $domain = 'default' ) {
	echo esc_attr( translate( $text, $domain ) );
}

