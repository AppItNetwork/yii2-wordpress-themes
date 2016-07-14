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

function _n_noop( $singular, $plural, $domain = null ) {
	return array( 0 => $singular, 1 => $plural, 'singular' => $singular, 'plural' => $plural, 'context' => null, 'domain' => $domain );
}

function load_theme_textdomain( $domain, $path = false ) {
	$locale = get_locale();
	$locale = apply_filters( 'theme_locale', $locale, $domain );

	if ( ! $path )
		$path = get_template_directory();

	// Load the textdomain according to the theme
	$mofile = untrailingslashit( $path ) . "/{$locale}.mo";
	if ( $loaded = load_textdomain( $domain, $mofile ) )
		return $loaded;

	// Otherwise, load from the languages directory
	$mofile = WP_LANG_DIR . "/themes/{$domain}-{$locale}.mo";
	return load_textdomain( $domain, $mofile );
}

function load_textdomain( $domain, $mofile ) {
	global $l10n;

	$plugin_override = apply_filters( 'override_load_textdomain', false, $domain, $mofile );

	if ( true == $plugin_override ) {
		return true;
	}

	do_action( 'load_textdomain', $domain, $mofile );

	$mofile = apply_filters( 'load_textdomain_mofile', $mofile, $domain );

	if ( !is_readable( $mofile ) ) return false;

	$mo = new MO();
	if ( !$mo->import_from_file( $mofile ) ) return false;

	if ( isset( $l10n[$domain] ) )
		$mo->merge_with( $l10n[$domain] );

	$l10n[$domain] = &$mo;

	return true;
}

function esc_attr__( $text, $domain = 'default' ) {
	return esc_attr( translate( $text, $domain ) );
}

