<?php

function wp_enqueue_scripts() {
	do_action( 'wp_enqueue_scripts' );
}

function wp_print_head_scripts() {
	if ( ! did_action('wp_print_scripts') ) {
		/** This action is documented in wp-includes/functions.wp-scripts.php */
		do_action( 'wp_print_scripts' );
	}

	global $wp_scripts;

	if ( ! ( $wp_scripts instanceof WP_Scripts ) ) {
		return array(); // no need to run if nothing is queued
	}
	return print_head_scripts();
}

function print_head_scripts() {
	global $concatenate_scripts;

	if ( ! did_action('wp_print_scripts') ) {
		/** This action is documented in wp-includes/functions.wp-scripts.php */
		do_action( 'wp_print_scripts' );
	}

	$wp_scripts = wp_scripts();

	script_concat_settings();
	$wp_scripts->do_concat = $concatenate_scripts;
	$wp_scripts->do_head_items();

	/**
	 * Filter whether to print the head scripts.
	 *
	 * @since 2.8.0
	 *
	 * @param bool $print Whether to print the head scripts. Default true.
	 */
	if ( apply_filters( 'print_head_scripts', true ) ) {
		_print_scripts();
	}

	$wp_scripts->reset();
	return $wp_scripts->done;
}

function script_concat_settings() {
	global $concatenate_scripts, $compress_scripts, $compress_css;

	$compressed_output = ( ini_get('zlib.output_compression') || 'ob_gzhandler' == ini_get('output_handler') );

	if ( ! isset($concatenate_scripts) ) {
		$concatenate_scripts = defined('CONCATENATE_SCRIPTS') ? CONCATENATE_SCRIPTS : true;
		if ( ! is_admin() || ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) )
			$concatenate_scripts = false;
	}

	if ( ! isset($compress_scripts) ) {
		$compress_scripts = defined('COMPRESS_SCRIPTS') ? COMPRESS_SCRIPTS : true;
		if ( $compress_scripts && ( ! get_site_option('can_compress_scripts') || $compressed_output ) )
			$compress_scripts = false;
	}

	if ( ! isset($compress_css) ) {
		$compress_css = defined('COMPRESS_CSS') ? COMPRESS_CSS : true;
		if ( $compress_css && ( ! get_site_option('can_compress_scripts') || $compressed_output ) )
			$compress_css = false;
	}
}

function wp_just_in_time_script_localization() {

	wp_localize_script( 'autosave', 'autosaveL10n', array(
		'autosaveInterval' => AUTOSAVE_INTERVAL,
		'blog_id' => get_current_blog_id(),
	) );

}

function wp_default_scripts( &$scripts ) {
	include( ABSPATH . WPINC . '/version.php' ); // include an unmodified $wp_version

	$develop_src = false !== strpos( $wp_version, '-src' );

	if ( ! defined( 'SCRIPT_DEBUG' ) ) {
		define( 'SCRIPT_DEBUG', $develop_src );
	}

	if ( ! $guessurl = site_url() ) {
		$guessed_url = true;
		$guessurl = wp_guess_url();
	}

	$wpAssetUrl = Yii::$app->wpthemes->getAssetUrl();
	$wpAdminAssetUrl = Yii::$app->wpthemes->getAdminAssetUrl();

	$scripts->base_url = $guessurl;
	$scripts->content_url = defined('WP_CONTENT_URL')? WP_CONTENT_URL : '';
	$scripts->default_version = get_bloginfo( 'version' );
	$scripts->default_dirs = array($wpAdminAssetUrl . '/js/', $wpAssetUrl . $wpAssetUrl . '/js/');

	$suffix = SCRIPT_DEBUG ? '' : '.min';
	$dev_suffix = $develop_src ? '' : '.min';

	$scripts->add( 'utils', $wpAssetUrl . "/js/utils$suffix.js" );
	did_action( 'init' ) && $scripts->localize( 'utils', 'userSettings', array(
		'url' => (string) SITECOOKIEPATH,
		'uid' => (string) get_current_user_id(),
		'time' => (string) time(),
		'secure' => (string) ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) ),
	) );

	$scripts->add( 'common', $wpAdminAssetUrl . "/js/common$suffix.js", array('jquery', 'hoverIntent', 'utils'), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'common', 'commonL10n', array(
		'warnDelete' => __( "You are about to permanently delete these items.\n  'Cancel' to stop, 'OK' to delete." ),
		'dismiss'    => __( 'Dismiss this notice.' ),
	) );

	$scripts->add( 'wp-a11y', $wpAssetUrl . "/js/wp-a11y$suffix.js", array( 'jquery' ), false, 1 );

	$scripts->add( 'sack', $wpAssetUrl . "/js/tw-sack$suffix.js", array(), '1.6.1', 1 );

	$scripts->add( 'quicktags', $wpAssetUrl . "/js/quicktags$suffix.js", array(), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'quicktags', 'quicktagsL10n', array(
		'closeAllOpenTags'      => __( 'Close all open tags' ),
		'closeTags'             => __( 'close tags' ),
		'enterURL'              => __( 'Enter the URL' ),
		'enterImageURL'         => __( 'Enter the URL of the image' ),
		'enterImageDescription' => __( 'Enter a description of the image' ),
		'textdirection'         => __( 'text direction' ),
		'toggleTextdirection'   => __( 'Toggle Editor Text Direction' ),
		'dfw'                   => __( 'Distraction-free writing mode' ),
		'strong'          => __( 'Bold' ),
		'strongClose'     => __( 'Close bold tag' ),
		'em'              => __( 'Italic' ),
		'emClose'         => __( 'Close italic tag' ),
		'link'            => __( 'Insert link' ),
		'blockquote'      => __( 'Blockquote' ),
		'blockquoteClose' => __( 'Close blockquote tag' ),
		'del'             => __( 'Deleted text (strikethrough)' ),
		'delClose'        => __( 'Close deleted text tag' ),
		'ins'             => __( 'Inserted text' ),
		'insClose'        => __( 'Close inserted text tag' ),
		'image'           => __( 'Insert image' ),
		'ul'              => __( 'Bulleted list' ),
		'ulClose'         => __( 'Close bulleted list tag' ),
		'ol'              => __( 'Numbered list' ),
		'olClose'         => __( 'Close numbered list tag' ),
		'li'              => __( 'List item' ),
		'liClose'         => __( 'Close list item tag' ),
		'code'            => __( 'Code' ),
		'codeClose'       => __( 'Close code tag' ),
		'more'            => __( 'Insert Read More tag' ),
	) );

	$scripts->add( 'colorpicker', $wpAssetUrl . "/js/colorpicker$suffix.js", array('prototype'), '3517m' );

	$scripts->add( 'editor', $wpAdminAssetUrl . "/js/editor$suffix.js", array('utils','jquery'), false, 1 );

	// Back-compat for old DFW. To-do: remove at the end of 2016.
	$scripts->add( 'wp-fullscreen-stub', $wpAdminAssetUrl . "/js/wp-fullscreen-stub$suffix.js", array(), false, 1 );

	$scripts->add( 'wp-ajax-response', $wpAssetUrl . "/js/wp-ajax-response$suffix.js", array('jquery'), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'wp-ajax-response', 'wpAjax', array(
		'noPerm' => __('You do not have permission to do that.'),
		'broken' => __('An unidentified error has occurred.')
	) );

	$scripts->add( 'wp-pointer', $wpAssetUrl . "/js/wp-pointer$suffix.js", array( 'jquery-ui-widget', 'jquery-ui-position' ), '20111129a', 1 );
	did_action( 'init' ) && $scripts->localize( 'wp-pointer', 'wpPointerL10n', array(
		'dismiss' => __('Dismiss'),
	) );

	$scripts->add( 'autosave', $wpAssetUrl . "/js/autosave$suffix.js", array('heartbeat'), false, 1 );

	$scripts->add( 'heartbeat', $wpAssetUrl . "/js/heartbeat$suffix.js", array('jquery'), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'heartbeat', 'heartbeatSettings',
		/**
		 * Filter the Heartbeat settings.
		 *
		 * @since 3.6.0
		 *
		 * @param array $settings Heartbeat settings array.
		 */
		apply_filters( 'heartbeat_settings', array() )
	);

	$scripts->add( 'wp-auth-check', $wpAssetUrl . "/js/wp-auth-check$suffix.js", array('heartbeat'), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'wp-auth-check', 'authcheckL10n', array(
		'beforeunload' => __('Your session has expired. You can log in again from this page or go to the login page.'),

		/**
		 * Filter the authentication check interval.
		 *
		 * @since 3.6.0
		 *
		 * @param int $interval The interval in which to check a user's authentication.
		 *                      Default 3 minutes in seconds, or 180.
		 */
		'interval' => apply_filters( 'wp_auth_check_interval', 3 * MINUTE_IN_SECONDS ),
	) );

	$scripts->add( 'wp-lists', $wpAssetUrl . "/js/wp-lists$suffix.js", array( 'wp-ajax-response', 'jquery-color' ), false, 1 );

	// WordPress no longer uses or bundles Prototype or script.aculo.us. These are now pulled from an external source.
	$scripts->add( 'prototype', 'https://ajax.googleapis.com/ajax/libs/prototype/1.7.1.0/prototype.js', array(), '1.7.1');
	$scripts->add( 'scriptaculous-root', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/scriptaculous.js', array('prototype'), '1.9.0');
	$scripts->add( 'scriptaculous-builder', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/builder.js', array('scriptaculous-root'), '1.9.0');
	$scripts->add( 'scriptaculous-dragdrop', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/dragdrop.js', array('scriptaculous-builder', 'scriptaculous-effects'), '1.9.0');
	$scripts->add( 'scriptaculous-effects', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/effects.js', array('scriptaculous-root'), '1.9.0');
	$scripts->add( 'scriptaculous-slider', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/slider.js', array('scriptaculous-effects'), '1.9.0');
	$scripts->add( 'scriptaculous-sound', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/sound.js', array( 'scriptaculous-root' ), '1.9.0' );
	$scripts->add( 'scriptaculous-controls', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/controls.js', array('scriptaculous-root'), '1.9.0');
	$scripts->add( 'scriptaculous', false, array('scriptaculous-dragdrop', 'scriptaculous-slider', 'scriptaculous-controls') );

	// not used in core, replaced by Jcrop.js
	$scripts->add( 'cropper', $wpAssetUrl . '/js/crop/cropper.js', array('scriptaculous-dragdrop') );

	// jQuery
	$scripts->add( 'jquery', false, array( 'jquery-core', 'jquery-migrate' ), '1.11.3' );
	$scripts->add( 'jquery-core', $wpAssetUrl . '/js/jquery/jquery.js', array(), '1.11.3' );
	$scripts->add( 'jquery-migrate', $wpAssetUrl . "/js/jquery/jquery-migrate$suffix.js", array(), '1.2.1' );

	// full jQuery UI
	$scripts->add( 'jquery-ui-core', $wpAssetUrl . "/js/jquery/ui/core$dev_suffix.js", array('jquery'), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-core', $wpAssetUrl . "/js/jquery/ui/effect$dev_suffix.js", array('jquery'), '1.11.4', 1 );

	$scripts->add( 'jquery-effects-blind', $wpAssetUrl . "/js/jquery/ui/effect-blind$dev_suffix.js", array('jquery-effects-core'), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-bounce', $wpAssetUrl . "/js/jquery/ui/effect-bounce$dev_suffix.js", array('jquery-effects-core'), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-clip', $wpAssetUrl . "/js/jquery/ui/effect-clip$dev_suffix.js", array('jquery-effects-core'), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-drop', $wpAssetUrl . "/js/jquery/ui/effect-drop$dev_suffix.js", array('jquery-effects-core'), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-explode', $wpAssetUrl . "/js/jquery/ui/effect-explode$dev_suffix.js", array('jquery-effects-core'), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-fade', $wpAssetUrl . "/js/jquery/ui/effect-fade$dev_suffix.js", array('jquery-effects-core'), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-fold', $wpAssetUrl . "/js/jquery/ui/effect-fold$dev_suffix.js", array('jquery-effects-core'), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-highlight', $wpAssetUrl . "/js/jquery/ui/effect-highlight$dev_suffix.js", array('jquery-effects-core'), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-puff', $wpAssetUrl . "/js/jquery/ui/effect-puff$dev_suffix.js", array('jquery-effects-core', 'jquery-effects-scale'), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-pulsate', $wpAssetUrl . "/js/jquery/ui/effect-pulsate$dev_suffix.js", array('jquery-effects-core'), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-scale', $wpAssetUrl . "/js/jquery/ui/effect-scale$dev_suffix.js", array('jquery-effects-core', 'jquery-effects-size'), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-shake', $wpAssetUrl . "/js/jquery/ui/effect-shake$dev_suffix.js", array('jquery-effects-core'), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-size', $wpAssetUrl . "/js/jquery/ui/effect-size$dev_suffix.js", array('jquery-effects-core'), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-slide', $wpAssetUrl . "/js/jquery/ui/effect-slide$dev_suffix.js", array('jquery-effects-core'), '1.11.4', 1 );
	$scripts->add( 'jquery-effects-transfer', $wpAssetUrl . "/js/jquery/ui/effect-transfer$dev_suffix.js", array('jquery-effects-core'), '1.11.4', 1 );

	$scripts->add( 'jquery-ui-accordion', $wpAssetUrl . "/js/jquery/ui/accordion$dev_suffix.js", array('jquery-ui-core', 'jquery-ui-widget'), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-autocomplete', $wpAssetUrl . "/js/jquery/ui/autocomplete$dev_suffix.js", array('jquery-ui-menu'), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-button', $wpAssetUrl . "/js/jquery/ui/button$dev_suffix.js", array('jquery-ui-core', 'jquery-ui-widget'), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-datepicker', $wpAssetUrl . "/js/jquery/ui/datepicker$dev_suffix.js", array('jquery-ui-core'), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-dialog', $wpAssetUrl . "/js/jquery/ui/dialog$dev_suffix.js", array('jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-button', 'jquery-ui-position'), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-draggable', $wpAssetUrl . "/js/jquery/ui/draggable$dev_suffix.js", array('jquery-ui-mouse'), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-droppable', $wpAssetUrl . "/js/jquery/ui/droppable$dev_suffix.js", array('jquery-ui-draggable'), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-menu', $wpAssetUrl . "/js/jquery/ui/menu$dev_suffix.js", array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-mouse', $wpAssetUrl . "/js/jquery/ui/mouse$dev_suffix.js", array( 'jquery-ui-core', 'jquery-ui-widget' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-position', $wpAssetUrl . "/js/jquery/ui/position$dev_suffix.js", array('jquery'), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-progressbar', $wpAssetUrl . "/js/jquery/ui/progressbar$dev_suffix.js", array('jquery-ui-core', 'jquery-ui-widget'), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-resizable', $wpAssetUrl . "/js/jquery/ui/resizable$dev_suffix.js", array('jquery-ui-mouse'), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-selectable', $wpAssetUrl . "/js/jquery/ui/selectable$dev_suffix.js", array('jquery-ui-mouse'), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-selectmenu', $wpAssetUrl . "/js/jquery/ui/selectmenu$dev_suffix.js", array('jquery-ui-menu'), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-slider', $wpAssetUrl . "/js/jquery/ui/slider$dev_suffix.js", array('jquery-ui-mouse'), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-sortable', $wpAssetUrl . "/js/jquery/ui/sortable$dev_suffix.js", array('jquery-ui-mouse'), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-spinner', $wpAssetUrl . "/js/jquery/ui/spinner$dev_suffix.js", array( 'jquery-ui-button' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-tabs', $wpAssetUrl . "/js/jquery/ui/tabs$dev_suffix.js", array('jquery-ui-core', 'jquery-ui-widget'), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-tooltip', $wpAssetUrl . "/js/jquery/ui/tooltip$dev_suffix.js", array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ), '1.11.4', 1 );
	$scripts->add( 'jquery-ui-widget', $wpAssetUrl . "/js/jquery/ui/widget$dev_suffix.js", array('jquery'), '1.11.4', 1 );

	// deprecated, not used in core, most functionality is included in jQuery 1.3
	$scripts->add( 'jquery-form', $wpAssetUrl . "/js/jquery/jquery.form$suffix.js", array('jquery'), '3.37.0', 1 );

	// jQuery plugins
	$scripts->add( 'jquery-color', $wpAssetUrl . "/js/jquery/jquery.color.min.js", array('jquery'), '2.1.1', 1 );
	$scripts->add( 'suggest', $wpAssetUrl . "/js/jquery/suggest$suffix.js", array('jquery'), '1.1-20110113', 1 );
	$scripts->add( 'schedule', $wpAssetUrl . '/js/jquery/jquery.schedule.js', array('jquery'), '20m', 1 );
	$scripts->add( 'jquery-query', $wpAssetUrl . "/js/jquery/jquery.query.js", array('jquery'), '2.1.7', 1 );
	$scripts->add( 'jquery-serialize-object', $wpAssetUrl . "/js/jquery/jquery.serialize-object.js", array('jquery'), '0.2', 1 );
	$scripts->add( 'jquery-hotkeys', $wpAssetUrl . "/js/jquery/jquery.hotkeys$suffix.js", array('jquery'), '0.0.2m', 1 );
	$scripts->add( 'jquery-table-hotkeys', $wpAssetUrl . "/js/jquery/jquery.table-hotkeys$suffix.js", array('jquery', 'jquery-hotkeys'), false, 1 );
	$scripts->add( 'jquery-touch-punch', $wpAssetUrl . "/js/jquery/jquery.ui.touch-punch.js", array('jquery-ui-widget', 'jquery-ui-mouse'), '0.2.2', 1 );

	// Masonry v2 depended on jQuery. v3 does not. The older jquery-masonry handle is a shiv.
	// It sets jQuery as a dependency, as the theme may have been implicitly loading it this way.
	$scripts->add( 'masonry', $wpAssetUrl . "/js/masonry.min.js", array(), '3.1.2', 1 );
	$scripts->add( 'jquery-masonry', $wpAssetUrl . "/js/jquery/jquery.masonry$dev_suffix.js", array( 'jquery', 'masonry' ), '3.1.2', 1 );

	$scripts->add( 'thickbox', $wpAssetUrl . "/js/thickbox/thickbox.js", array('jquery'), '3.1-20121105', 1 );
	did_action( 'init' ) && $scripts->localize( 'thickbox', 'thickboxL10n', array(
			'next' => __('Next &gt;'),
			'prev' => __('&lt; Prev'),
			'image' => __('Image'),
			'of' => __('of'),
			'close' => __('Close'),
			'noiframes' => __('This feature requires inline frames. You have iframes disabled or your browser does not support them.'),
			'loadingAnimation' => includes_url('js/thickbox/loadingAnimation.gif'),
	) );

	$scripts->add( 'jcrop', $wpAssetUrl . "/js/jcrop/jquery.Jcrop.min.js", array('jquery'), '0.9.12');

	$scripts->add( 'swfobject', $wpAssetUrl . "/js/swfobject.js", array(), '2.2-20120417');

	// error message for both plupload and swfupload
	$uploader_l10n = array(
		'queue_limit_exceeded' => __('You have attempted to queue too many files.'),
		'file_exceeds_size_limit' => __('%s exceeds the maximum upload size for this site.'),
		'zero_byte_file' => __('This file is empty. Please try another.'),
		'invalid_filetype' => __('This file type is not allowed. Please try another.'),
		'not_an_image' => __('This file is not an image. Please try another.'),
		'image_memory_exceeded' => __('Memory exceeded. Please try another smaller file.'),
		'image_dimensions_exceeded' => __('This is larger than the maximum size. Please try another.'),
		'default_error' => __('An error occurred in the upload. Please try again later.'),
		'missing_upload_url' => __('There was a configuration error. Please contact the server administrator.'),
		'upload_limit_exceeded' => __('You may only upload 1 file.'),
		'http_error' => __('HTTP error.'),
		'upload_failed' => __('Upload failed.'),
		'big_upload_failed' => __('Please try uploading this file with the %1$sbrowser uploader%2$s.'),
		'big_upload_queued' => __('%s exceeds the maximum upload size for the multi-file uploader when used in your browser.'),
		'io_error' => __('IO error.'),
		'security_error' => __('Security error.'),
		'file_cancelled' => __('File canceled.'),
		'upload_stopped' => __('Upload stopped.'),
		'dismiss' => __('Dismiss'),
		'crunching' => __('Crunching&hellip;'),
		'deleted' => __('moved to the trash.'),
		'error_uploading' => __('&#8220;%s&#8221; has failed to upload.')
	);

	$scripts->add( 'plupload', $wpAssetUrl . '/js/plupload/plupload.full.min.js', array(), '2.1.8' );
	// Back compat handles:
	foreach ( array( 'all', 'html5', 'flash', 'silverlight', 'html4' ) as $handle ) {
		$scripts->add( "plupload-$handle", false, array( 'plupload' ), '2.1.1' );
	}

	$scripts->add( 'plupload-handlers', $wpAssetUrl . "/js/plupload/handlers$suffix.js", array( 'plupload', 'jquery' ) );
	did_action( 'init' ) && $scripts->localize( 'plupload-handlers', 'pluploadL10n', $uploader_l10n );

	$scripts->add( 'wp-plupload', $wpAssetUrl . "/js/plupload/wp-plupload$suffix.js", array( 'plupload', 'jquery', 'json2', 'media-models' ), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'wp-plupload', 'pluploadL10n', $uploader_l10n );

	// keep 'swfupload' for back-compat.
	$scripts->add( 'swfupload', $wpAssetUrl . '/js/swfupload/swfupload.js', array(), '2201-20110113');
	$scripts->add( 'swfupload-swfobject', $wpAssetUrl . '/js/swfupload/plugins/swfupload.swfobject.js', array('swfupload', 'swfobject'), '2201a');
	$scripts->add( 'swfupload-queue', $wpAssetUrl . '/js/swfupload/plugins/swfupload.queue.js', array('swfupload'), '2201');
	$scripts->add( 'swfupload-speed', $wpAssetUrl . '/js/swfupload/plugins/swfupload.speed.js', array('swfupload'), '2201');
	$scripts->add( 'swfupload-all', false, array('swfupload', 'swfupload-swfobject', 'swfupload-queue'), '2201');
	$scripts->add( 'swfupload-handlers', $wpAssetUrl . "/js/swfupload/handlers$suffix.js", array('swfupload-all', 'jquery'), '2201-20110524');
	did_action( 'init' ) && $scripts->localize( 'swfupload-handlers', 'swfuploadL10n', $uploader_l10n );

	$scripts->add( 'comment-reply', $wpAssetUrl . "/js/comment-reply$suffix.js", array(), false, 1 );

	$scripts->add( 'json2', $wpAssetUrl . "/js/json2$suffix.js", array(), '2015-05-03' );
	did_action( 'init' ) && $scripts->add_data( 'json2', 'conditional', 'lt IE 8' );

	$scripts->add( 'underscore', $wpAssetUrl . "/js/underscore$dev_suffix.js", array(), '1.6.0', 1 );
	$scripts->add( 'backbone', $wpAssetUrl . "/js/backbone$dev_suffix.js", array( 'underscore','jquery' ), '1.1.2', 1 );

	$scripts->add( 'wp-util', $wpAssetUrl . "/js/wp-util$suffix.js", array('underscore', 'jquery'), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'wp-util', '_wpUtilSettings', array(
		'ajax' => array(
			'url' => admin_url( 'admin-ajax.php', 'relative' ),
		),
	) );

	$scripts->add( 'wp-backbone', $wpAssetUrl . "/js/wp-backbone$suffix.js", array('backbone', 'wp-util'), false, 1 );

	$scripts->add( 'revisions', $wpAdminAssetUrl . "/js/revisions$suffix.js", array( 'wp-backbone', 'jquery-ui-slider', 'hoverIntent' ), false, 1 );

	$scripts->add( 'imgareaselect', $wpAssetUrl . "/js/imgareaselect/jquery.imgareaselect$suffix.js", array('jquery'), false, 1 );

	$scripts->add( 'mediaelement', $wpAssetUrl . "/js/mediaelement/mediaelement-and-player.min.js", array('jquery'), '2.18.1', 1 );
	did_action( 'init' ) && $scripts->localize( 'mediaelement', 'mejsL10n', array(
		'language' => get_bloginfo( 'language' ),
		'strings'  => array(
			'Close'               => __( 'Close' ),
			'Fullscreen'          => __( 'Fullscreen' ),
			'Download File'       => __( 'Download File' ),
			'Download Video'      => __( 'Download Video' ),
			'Play/Pause'          => __( 'Play/Pause' ),
			'Mute Toggle'         => __( 'Mute Toggle' ),
			'None'                => __( 'None' ),
			'Turn off Fullscreen' => __( 'Turn off Fullscreen' ),
			'Go Fullscreen'       => __( 'Go Fullscreen' ),
			'Unmute'              => __( 'Unmute' ),
			'Mute'                => __( 'Mute' ),
			'Captions/Subtitles'  => __( 'Captions/Subtitles' )
		),
	) );


	$scripts->add( 'wp-mediaelement', $wpAssetUrl . "/js/mediaelement/wp-mediaelement.js", array('mediaelement'), false, 1 );
	$mejs_settings = array(
		'pluginPath' => includes_url( 'js/mediaelement/', 'relative' ),
	);
	did_action( 'init' ) && $scripts->localize( 'mediaelement', '_wpmejsSettings',
		/**
		 * Filter the MediaElement configuration settings.
		 *
		 * @since 4.4.0
		 *
		 * @param array $mejs_settings MediaElement settings array.
		 */
		apply_filters( 'mejs_settings', $mejs_settings )
	);

	$scripts->add( 'froogaloop',  $wpAssetUrl . "/js/mediaelement/froogaloop.min.js", array(), '2.0' );
	$scripts->add( 'wp-playlist', $wpAssetUrl . "/js/mediaelement/wp-playlist.js", array( 'wp-util', 'backbone', 'mediaelement' ), false, 1 );

	$scripts->add( 'zxcvbn-async', $wpAssetUrl . "/js/zxcvbn-async$suffix.js", array(), '1.0' );
	did_action( 'init' ) && $scripts->localize( 'zxcvbn-async', '_zxcvbnSettings', array(
		'src' => empty( $guessed_url ) ? includes_url( '/js/zxcvbn.min.js' ) : $scripts->base_url . $wpAssetUrl . '/js/zxcvbn.min.js',
	) );

	$scripts->add( 'password-strength-meter', $wpAdminAssetUrl . "/js/password-strength-meter$suffix.js", array( 'jquery', 'zxcvbn-async' ), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'password-strength-meter', 'pwsL10n', array(
		'short'    => _x( 'Very weak', 'password strength' ),
		'bad'      => _x( 'Weak', 'password strength' ),
		'good'     => _x( 'Medium', 'password strength' ),
		'strong'   => _x( 'Strong', 'password strength' ),
		'mismatch' => _x( 'Mismatch', 'password mismatch' ),
	) );

	$scripts->add( 'user-profile', $wpAdminAssetUrl . "/js/user-profile$suffix.js", array( 'jquery', 'password-strength-meter', 'wp-util' ), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'user-profile', 'userProfileL10n', array(
		'warn'     => __( 'Your new password has not been saved.' ),
		'show'     => __( 'Show' ),
		'hide'     => __( 'Hide' ),
		'cancel'   => __( 'Cancel' ),
		'ariaShow' => esc_attr__( 'Show password' ),
		'ariaHide' => esc_attr__( 'Hide password' ),
	) );

	$scripts->add( 'language-chooser', $wpAdminAssetUrl . "/js/language-chooser$suffix.js", array( 'jquery' ), false, 1 );

	$scripts->add( 'user-suggest', $wpAdminAssetUrl . "/js/user-suggest$suffix.js", array( 'jquery-ui-autocomplete' ), false, 1 );

	$scripts->add( 'admin-bar', $wpAssetUrl . "/js/admin-bar$suffix.js", array(), false, 1 );

	$scripts->add( 'wplink', $wpAssetUrl . "/js/wplink$suffix.js", array( 'jquery' ), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'wplink', 'wpLinkL10n', array(
		'title' => __('Insert/edit link'),
		'update' => __('Update'),
		'save' => __('Add Link'),
		'noTitle' => __('(no title)'),
		'noMatchesFound' => __('No results found.')
	) );

	$scripts->add( 'wpdialogs', $wpAssetUrl . "/js/wpdialog$suffix.js", array( 'jquery-ui-dialog' ), false, 1 );

	$scripts->add( 'word-count', $wpAdminAssetUrl . "/js/word-count$suffix.js", array(), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'word-count', 'wordCountL10n', array(
		/*
		 * translators: If your word count is based on single characters (e.g. East Asian characters),
		 * enter 'characters_excluding_spaces' or 'characters_including_spaces'. Otherwise, enter 'words'.
		 * Do not translate into your own language.
		 */
		'type' => _x( 'words', 'Word count type. Do not translate!' ),
		'shortcodes' => ! empty( $GLOBALS['shortcode_tags'] ) ? array_keys( $GLOBALS['shortcode_tags'] ) : array()
	) );

	$scripts->add( 'media-upload', $wpAdminAssetUrl . "/js/media-upload$suffix.js", array( 'thickbox', 'shortcode' ), false, 1 );

	$scripts->add( 'hoverIntent', $wpAssetUrl . "/js/hoverIntent$suffix.js", array('jquery'), '1.8.1', 1 );

	$scripts->add( 'customize-base',     $wpAssetUrl . "/js/customize-base$suffix.js",     array( 'jquery', 'json2', 'underscore' ), false, 1 );
	$scripts->add( 'customize-loader',   $wpAssetUrl . "/js/customize-loader$suffix.js",   array( 'customize-base' ), false, 1 );
	$scripts->add( 'customize-preview',  $wpAssetUrl . "/js/customize-preview$suffix.js",  array( 'customize-base' ), false, 1 );
	$scripts->add( 'customize-models',   $wpAssetUrl . "/js/customize-models.js", array( 'underscore', 'backbone' ), false, 1 );
	$scripts->add( 'customize-views',    $wpAssetUrl . "/js/customize-views.js",  array( 'jquery', 'underscore', 'imgareaselect', 'customize-models', 'media-editor', 'media-views' ), false, 1 );
	$scripts->add( 'customize-controls', $wpAdminAssetUrl . "/js/customize-controls$suffix.js", array( 'customize-base', 'wp-a11y' ), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'customize-controls', '_wpCustomizeControlsL10n', array(
		'activate'           => __( 'Save &amp; Activate' ),
		'save'               => __( 'Save &amp; Publish' ),
		'saveAlert'          => __( 'The changes you made will be lost if you navigate away from this page.' ),
		'saved'              => __( 'Saved' ),
		'cancel'             => __( 'Cancel' ),
		'close'              => __( 'Close' ),
		'cheatin'            => __( 'Cheatin&#8217; uh?' ),
		'notAllowed'         => __( 'You are not allowed to customize the appearance of this site.' ),
		'previewIframeTitle' => __( 'Site Preview' ),
		'loginIframeTitle'   => __( 'Session expired' ),
		'collapseSidebar'    => __( 'Collapse Sidebar' ),
		'expandSidebar'      => __( 'Expand Sidebar' ),
		// Used for overriding the file types allowed in plupload.
		'allowedFiles'       => __( 'Allowed Files' ),
	) );

	$scripts->add( 'customize-widgets', $wpAdminAssetUrl . "/js/customize-widgets$suffix.js", array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-droppable', 'wp-backbone', 'customize-controls' ), false, 1 );
	$scripts->add( 'customize-preview-widgets', $wpAssetUrl . "/js/customize-preview-widgets$suffix.js", array( 'jquery', 'wp-util', 'customize-preview' ), false, 1 );

	$scripts->add( 'customize-nav-menus', $wpAdminAssetUrl . "/js/customize-nav-menus$suffix.js", array( 'jquery', 'wp-backbone', 'customize-controls', 'accordion', 'nav-menu' ), false, 1 );
	$scripts->add( 'customize-preview-nav-menus', $wpAssetUrl . "/js/customize-preview-nav-menus$suffix.js", array( 'jquery', 'wp-util', 'customize-preview' ), false, 1 );

	$scripts->add( 'accordion', $wpAdminAssetUrl . "/js/accordion$suffix.js", array( 'jquery' ), false, 1 );

	$scripts->add( 'shortcode', $wpAssetUrl . "/js/shortcode$suffix.js", array( 'underscore' ), false, 1 );
	$scripts->add( 'media-models', $wpAssetUrl . "/js/media-models$suffix.js", array( 'wp-backbone' ), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'media-models', '_wpMediaModelsL10n', array(
		'settings' => array(
			'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ),
			'post' => array( 'id' => 0 ),
		),
	) );

	$scripts->add( 'wp-embed', $wpAssetUrl . "/js/wp-embed$suffix.js" );

	// To enqueue media-views or media-editor, call wp_enqueue_media().
	// Both rely on numerous settings, styles, and templates to operate correctly.
	$scripts->add( 'media-views',  $wpAssetUrl . "/js/media-views$suffix.js",  array( 'utils', 'media-models', 'wp-plupload', 'jquery-ui-sortable', 'wp-mediaelement' ), false, 1 );
	$scripts->add( 'media-editor', $wpAssetUrl . "/js/media-editor$suffix.js", array( 'shortcode', 'media-views' ), false, 1 );
	$scripts->add( 'media-audiovideo', $wpAssetUrl . "/js/media-audiovideo$suffix.js", array( 'media-editor' ), false, 1 );
	$scripts->add( 'mce-view', $wpAssetUrl . "/js/mce-view$suffix.js", array( 'shortcode', 'jquery', 'media-views', 'media-audiovideo' ), false, 1 );

	if ( is_admin() ) {
		$scripts->add( 'admin-tags', $wpAdminAssetUrl . "/js/tags$suffix.js", array( 'jquery', 'wp-ajax-response' ), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'admin-tags', 'tagsl10n', array(
			'noPerm' => __('You do not have permission to do that.'),
			'broken' => __('An unidentified error has occurred.')
		));

		$scripts->add( 'admin-comments', $wpAdminAssetUrl . "/js/edit-comments$suffix.js", array('wp-lists', 'quicktags', 'jquery-query'), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'admin-comments', 'adminCommentsL10n', array(
			'hotkeys_highlight_first' => isset($_GET['hotkeys_highlight_first']),
			'hotkeys_highlight_last' => isset($_GET['hotkeys_highlight_last']),
			'replyApprove' => __( 'Approve and Reply' ),
			'reply' => __( 'Reply' ),
			'warnQuickEdit' => __( "Are you sure you want to edit this comment?\nThe changes you made will be lost." ),
			'docTitleComments' => __( 'Comments' ),
			/* translators: %s: comments count */
			'docTitleCommentsCount' => __( 'Comments (%s)' ),
		) );

		$scripts->add( 'xfn', $wpAdminAssetUrl . "/js/xfn$suffix.js", array('jquery'), false, 1 );

		$scripts->add( 'postbox', $wpAdminAssetUrl . "/js/postbox$suffix.js", array('jquery-ui-sortable'), false, 1 );

		$scripts->add( 'tags-box', $wpAdminAssetUrl . "/js/tags-box$suffix.js", array( 'jquery', 'suggest' ), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'tags-box', 'tagsBoxL10n', array(
			'tagDelimiter' => _x( ',', 'tag delimiter' ),
		) );

		$scripts->add( 'post', $wpAdminAssetUrl . "/js/post$suffix.js", array( 'suggest', 'wp-lists', 'postbox', 'tags-box', 'underscore', 'word-count', 'wp-a11y' ), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'post', 'postL10n', array(
			'ok' => __('OK'),
			'cancel' => __('Cancel'),
			'publishOn' => __('Publish on:'),
			'publishOnFuture' =>  __('Schedule for:'),
			'publishOnPast' => __('Published on:'),
			/* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
			'dateFormat' => __('%1$s %2$s, %3$s @ %4$s:%5$s'),
			'showcomm' => __('Show more comments'),
			'endcomm' => __('No more comments found.'),
			'publish' => __('Publish'),
			'schedule' => __('Schedule'),
			'update' => __('Update'),
			'savePending' => __('Save as Pending'),
			'saveDraft' => __('Save Draft'),
			'private' => __('Private'),
			'public' => __('Public'),
			'publicSticky' => __('Public, Sticky'),
			'password' => __('Password Protected'),
			'privatelyPublished' => __('Privately Published'),
			'published' => __('Published'),
			'saveAlert' => __('The changes you made will be lost if you navigate away from this page.'),
			'savingText' => __('Saving Draft&#8230;'),
			'permalinkSaved' => __( 'Permalink saved' ),
		) );

		$scripts->add( 'press-this', $wpAdminAssetUrl . "/js/press-this$suffix.js", array( 'jquery', 'tags-box' ), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'press-this', 'pressThisL10n', array(
			'newPost' => __( 'Title' ),
			'serverError' => __( 'Connection lost or the server is busy. Please try again later.' ),
			'saveAlert' => __( 'The changes you made will be lost if you navigate away from this page.' ),
			/* translators: %d: nth embed found in a post */
			'suggestedEmbedAlt' => __( 'Suggested embed #%d' ),
			/* translators: %d: nth image found in a post */
			'suggestedImgAlt' => __( 'Suggested image #%d' ),
		) );

		$scripts->add( 'editor-expand', $wpAdminAssetUrl . "/js/editor-expand$suffix.js", array( 'jquery' ), false, 1 );

		$scripts->add( 'link', $wpAdminAssetUrl . "/js/link$suffix.js", array( 'wp-lists', 'postbox' ), false, 1 );

		$scripts->add( 'comment', $wpAdminAssetUrl . "/js/comment$suffix.js", array( 'jquery', 'postbox' ) );
		$scripts->add_data( 'comment', 'group', 1 );
		did_action( 'init' ) && $scripts->localize( 'comment', 'commentL10n', array(
			'submittedOn' => __( 'Submitted on:' ),
			/* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
			'dateFormat' => __( '%1$s %2$s, %3$s @ %4$s:%5$s' )
		) );

		$scripts->add( 'admin-gallery', $wpAdminAssetUrl . "/js/gallery$suffix.js", array( 'jquery-ui-sortable' ) );

		$scripts->add( 'admin-widgets', $wpAdminAssetUrl . "/js/widgets$suffix.js", array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ), false, 1 );

		$scripts->add( 'theme', $wpAdminAssetUrl . "/js/theme$suffix.js", array( 'wp-backbone', 'wp-a11y' ), false, 1 );

		$scripts->add( 'inline-edit-post', $wpAdminAssetUrl . "/js/inline-edit-post$suffix.js", array( 'jquery', 'suggest' ), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'inline-edit-post', 'inlineEditL10n', array(
			'error' => __('Error while saving the changes.'),
			'ntdeltitle' => __('Remove From Bulk Edit'),
			'notitle' => __('(no title)'),
			'comma' => trim( _x( ',', 'tag delimiter' ) ),
		) );

		$scripts->add( 'inline-edit-tax', $wpAdminAssetUrl . "/js/inline-edit-tax$suffix.js", array( 'jquery', 'wp-a11y' ), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'inline-edit-tax', 'inlineEditL10n', array(
			'error' => __( 'Error while saving the changes.' ),
			'saved' => __( 'Changes saved.' ),
		) );

		$scripts->add( 'plugin-install', $wpAdminAssetUrl . "/js/plugin-install$suffix.js", array( 'jquery', 'thickbox' ), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'plugin-install', 'plugininstallL10n', array(
			'plugin_information' => __('Plugin Information:'),
			'ays' => __('Are you sure you want to install this plugin?')
		) );

		$scripts->add( 'updates', $wpAdminAssetUrl . "/js/updates$suffix.js", array( 'jquery', 'wp-util', 'wp-a11y' ) );
		did_action( 'init' ) && $scripts->localize( 'updates', '_wpUpdatesSettings', array(
			'ajax_nonce' => wp_create_nonce( 'updates' ),
			'l10n'       => array(
				'updating'          => __( 'Updating...' ), // no ellipsis
				'updated'           => __( 'Updated!' ),
				'updateFailedShort' => __( 'Update Failed!' ),
				/* translators: Error string for a failed update */
				'updateFailed'      => __( 'Update Failed: %s' ),
				/* translators: Plugin name and version */
				'updatingLabel'     => __( 'Updating %s...' ), // no ellipsis
				/* translators: Plugin name and version */
				'updatedLabel'      => __( '%s updated!' ),
				/* translators: Plugin name and version */
				'updateFailedLabel' => __( '%s update failed' ),
				/* translators: JavaScript accessible string */
				'updatingMsg'       => __( 'Updating... please wait.' ), // no ellipsis
				/* translators: JavaScript accessible string */
				'updatedMsg'        => __( 'Update completed successfully.' ),
				/* translators: JavaScript accessible string */
				'updateCancel'      => __( 'Update canceled.' ),
				'beforeunload'      => __( 'Plugin updates may not complete if you navigate away from this page.' ),
			)
		) );

		$scripts->add( 'farbtastic', $wpAdminAssetUrl . '/js/farbtastic.js', array('jquery'), '1.2' );

		$scripts->add( 'iris', $wpAdminAssetUrl . '/js/iris.min.js', array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), '1.0.7', 1 );
		$scripts->add( 'wp-color-picker', $wpAdminAssetUrl . "/js/color-picker$suffix.js", array( 'iris' ), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'wp-color-picker', 'wpColorPickerL10n', array(
			'clear' => __( 'Clear' ),
			'defaultString' => __( 'Default' ),
			'pick' => __( 'Select Color' ),
			'current' => __( 'Current Color' ),
		) );

		$scripts->add( 'dashboard', $wpAdminAssetUrl . "/js/dashboard$suffix.js", array( 'jquery', 'admin-comments', 'postbox' ), false, 1 );

		$scripts->add( 'list-revisions', $wpAssetUrl . "/js/wp-list-revisions$suffix.js" );

		$scripts->add( 'media-grid', $wpAssetUrl . "/js/media-grid$suffix.js", array( 'media-editor' ), false, 1 );
		$scripts->add( 'media', $wpAdminAssetUrl . "/js/media$suffix.js", array( 'jquery' ), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'media', 'attachMediaBoxL10n', array(
			'error' => __( 'An error has occurred. Please reload the page and try again.' ),
		));

		$scripts->add( 'image-edit', $wpAdminAssetUrl . "/js/image-edit$suffix.js", array('jquery', 'json2', 'imgareaselect'), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'image-edit', 'imageEditL10n', array(
			'error' => __( 'Could not load the preview image. Please reload the page and try again.' )
		));

		$scripts->add( 'set-post-thumbnail', $wpAdminAssetUrl . "/js/set-post-thumbnail$suffix.js", array( 'jquery' ), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'set-post-thumbnail', 'setPostThumbnailL10n', array(
			'setThumbnail' => __( 'Use as featured image' ),
			'saving' => __( 'Saving...' ), // no ellipsis
			'error' => __( 'Could not set that as the thumbnail image. Try a different attachment.' ),
			'done' => __( 'Done' )
		) );

		// Navigation Menus
		$scripts->add( 'nav-menu', $wpAdminAssetUrl . "/js/nav-menu$suffix.js", array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'wp-lists', 'postbox' ) );
		did_action( 'init' ) && $scripts->localize( 'nav-menu', 'navMenuL10n', array(
			'noResultsFound' => __( 'No results found.' ),
			'warnDeleteMenu' => __( "You are about to permanently delete this menu. \n 'Cancel' to stop, 'OK' to delete." ),
			'saveAlert' => __( 'The changes you made will be lost if you navigate away from this page.' ),
			'untitled' => _x( '(no label)', 'missing menu item navigation label' )
		) );

		$scripts->add( 'custom-header', $wpAdminAssetUrl . "/js/custom-header.js", array( 'jquery-masonry' ), false, 1 );
		$scripts->add( 'custom-background', $wpAdminAssetUrl . "/js/custom-background$suffix.js", array( 'wp-color-picker', 'media-views' ), false, 1 );
		$scripts->add( 'media-gallery', $wpAdminAssetUrl . "/js/media-gallery$suffix.js", array('jquery'), false, 1 );

		$scripts->add( 'svg-painter', $wpAdminAssetUrl . '/js/svg-painter.js', array( 'jquery' ), false, 1 );
	}
}

function wp_print_footer_scripts() {
	do_action( 'wp_print_footer_scripts' );
}

function _wp_footer_scripts() {
	print_late_styles();
	print_footer_scripts();
}

function print_late_styles() {
	global $wp_styles, $concatenate_scripts;

	if ( ! ( $wp_styles instanceof WP_Styles ) ) {
		return;
	}

	$wp_styles->do_concat = $concatenate_scripts;
	$wp_styles->do_footer_items();

	if ( apply_filters( 'print_late_styles', true ) ) {
		_print_styles();
	}

	$wp_styles->reset();
	return $wp_styles->done;
}

function print_footer_scripts() {
	global $wp_scripts, $concatenate_scripts;

	if ( ! ( $wp_scripts instanceof WP_Scripts ) ) {
		return array(); // No need to run if not instantiated.
	}
	script_concat_settings();
	$wp_scripts->do_concat = $concatenate_scripts;
	$wp_scripts->do_footer_items();

	if ( apply_filters( 'print_footer_scripts', true ) ) {
		_print_scripts();
	}

	$wp_scripts->reset();
	return $wp_scripts->done;
}

function wp_prototype_before_jquery( $js_array ) {
	if ( false === $prototype = array_search( 'prototype', $js_array, true ) )
		return $js_array;

	if ( false === $jquery = array_search( 'jquery', $js_array, true ) )
		return $js_array;

	if ( $prototype < $jquery )
		return $js_array;

	unset($js_array[$prototype]);

	array_splice( $js_array, $jquery, 0, 'prototype' );

	return $js_array;
}

function _print_scripts() {
	global $wp_scripts, $compress_scripts;

	$zip = $compress_scripts ? 1 : 0;
	if ( $zip && defined('ENFORCE_GZIP') && ENFORCE_GZIP )
		$zip = 'gzip';

	if ( $concat = trim( $wp_scripts->concat, ', ' ) ) {

		if ( !empty($wp_scripts->print_code) ) {
			echo "\n<script type='text/javascript'>\n";
			echo "/* <![CDATA[ */\n"; // not needed in HTML 5
			echo $wp_scripts->print_code;
			echo "/* ]]> */\n";
			echo "</script>\n";
		}

		$concat = str_split( $concat, 128 );
		$concat = 'load%5B%5D=' . implode( '&load%5B%5D=', $concat );

		$src = $wp_scripts->base_url . "/wp-admin/load-scripts.php?c={$zip}&" . $concat . '&ver=' . $wp_scripts->default_version;
		echo "<script type='text/javascript' src='" . esc_attr($src) . "'></script>\n";
	}

	if ( !empty($wp_scripts->print_html) )
		echo $wp_scripts->print_html;
}

function wp_default_styles( &$styles ) {
	include( ABSPATH . WPINC . '/version.php' ); // include an unmodified $wp_version

	if ( ! defined( 'SCRIPT_DEBUG' ) )
		define( 'SCRIPT_DEBUG', false !== strpos( $wp_version, '-src' ) );

	if ( ! $guessurl = site_url() )
		$guessurl = wp_guess_url();

	$wpAssetUrl = Yii::$app->wpthemes->getAssetUrl();
	$wpAdminAssetUrl = Yii::$app->wpthemes->getAdminAssetUrl();

	$styles->base_url = $guessurl;
	$styles->content_url = defined('WP_CONTENT_URL')? WP_CONTENT_URL : '';
	$styles->default_version = get_bloginfo( 'version' );
	$styles->text_direction = function_exists( 'is_rtl' ) && is_rtl() ? 'rtl' : 'ltr';
	$styles->default_dirs = array('/wp-admin/', $wpAssetUrl . '/css/');

	$open_sans_font_url = '';

	/* translators: If there are characters in your language that are not supported
	 * by Open Sans, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Open Sans font: on or off' ) ) {
		$subsets = 'latin,latin-ext';

		/* translators: To add an additional Open Sans character subset specific to your language,
		 * translate this to 'greek', 'cyrillic' or 'vietnamese'. Do not translate into your own language.
		 */
		$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)' );

		if ( 'cyrillic' == $subset ) {
			$subsets .= ',cyrillic,cyrillic-ext';
		} elseif ( 'greek' == $subset ) {
			$subsets .= ',greek,greek-ext';
		} elseif ( 'vietnamese' == $subset ) {
			$subsets .= ',vietnamese';
		}

		// Hotlink Open Sans, for now
		$open_sans_font_url = "https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,300,400,600&subset=$subsets";
	}

	// Register a stylesheet for the selected admin color scheme.
	$styles->add( 'colors', true, array( 'wp-admin', 'buttons', 'open-sans', 'dashicons' ) );

	$suffix = SCRIPT_DEBUG ? '' : '.min';

	// Admin CSS
	$styles->add( 'wp-admin',            $wpAdminAssetUrl . "/css/wp-admin$suffix.css", array( 'open-sans', 'dashicons' ) );
	$styles->add( 'login',               $wpAdminAssetUrl . "/css/login$suffix.css", array( 'buttons', 'open-sans', 'dashicons' ) );
	$styles->add( 'install',             $wpAdminAssetUrl . "/css/install$suffix.css", array( 'buttons', 'open-sans' ) );
	$styles->add( 'wp-color-picker',     $wpAdminAssetUrl . "/css/color-picker$suffix.css" );
	$styles->add( 'customize-controls',  $wpAdminAssetUrl . "/css/customize-controls$suffix.css", array( 'wp-admin', 'colors', 'ie', 'imgareaselect' ) );
	$styles->add( 'customize-widgets',   $wpAdminAssetUrl . "/css/customize-widgets$suffix.css", array( 'wp-admin', 'colors' ) );
	$styles->add( 'customize-nav-menus', $wpAdminAssetUrl . "/css/customize-nav-menus$suffix.css", array( 'wp-admin', 'colors' ) );
	$styles->add( 'press-this',          $wpAdminAssetUrl . "/css/press-this$suffix.css", array( 'open-sans', 'buttons' ) );

	$styles->add( 'ie', $wpAdminAssetUrl . "/css/ie$suffix.css" );
	$styles->add_data( 'ie', 'conditional', 'lte IE 7' );

	// Common dependencies
	$styles->add( 'buttons',   $wpAssetUrl . "/css/buttons$suffix.css" );
	$styles->add( 'dashicons', $wpAssetUrl . "/css/dashicons$suffix.css" );
	$styles->add( 'open-sans', $open_sans_font_url );

	// Includes CSS
	$styles->add( 'admin-bar',            $wpAssetUrl . "/css/admin-bar$suffix.css", array( 'open-sans', 'dashicons' ) );
	$styles->add( 'wp-auth-check',        $wpAssetUrl . "/css/wp-auth-check$suffix.css", array( 'dashicons' ) );
	$styles->add( 'editor-buttons',       $wpAssetUrl . "/css/editor$suffix.css", array( 'dashicons' ) );
	$styles->add( 'media-views',          $wpAssetUrl . "/css/media-views$suffix.css", array( 'buttons', 'dashicons', 'wp-mediaelement' ) );
	$styles->add( 'wp-pointer',           $wpAssetUrl . "/css/wp-pointer$suffix.css", array( 'dashicons' ) );
	$styles->add( 'customize-preview',    $wpAssetUrl . "/css/customize-preview$suffix.css" );
	$styles->add( 'wp-embed-template-ie', $wpAssetUrl . "/css/wp-embed-template-ie$suffix.css" );
	$styles->add_data( 'wp-embed-template-ie', 'conditional', 'lte IE 8' );

	// External libraries and friends
	$styles->add( 'imgareaselect',       $wpAssetUrl . '/js/imgareaselect/imgareaselect.css', array(), '0.9.8' );
	$styles->add( 'wp-jquery-ui-dialog', $wpAssetUrl . "/css/jquery-ui-dialog$suffix.css", array( 'dashicons' ) );
	$styles->add( 'mediaelement',        $wpAssetUrl . "/js/mediaelement/mediaelementplayer.min.css", array(), '2.18.1' );
	$styles->add( 'wp-mediaelement',     $wpAssetUrl . "/js/mediaelement/wp-mediaelement.css", array( 'mediaelement' ) );
	$styles->add( 'thickbox',            $wpAssetUrl . '/js/thickbox/thickbox.css', array( 'dashicons' ) );

	// Deprecated CSS
	$styles->add( 'media',      $wpAdminAssetUrl . "/css/deprecated-media$suffix.css" );
	$styles->add( 'farbtastic', $wpAdminAssetUrl . '/css/farbtastic.css', array(), '1.3u1' );
	$styles->add( 'jcrop',      $wpAssetUrl . "/js/jcrop/jquery.Jcrop.min.css", array(), '0.9.12' );
	$styles->add( 'colors-fresh', false, array( 'wp-admin', 'buttons' ) ); // Old handle.

	// RTL CSS
	$rtl_styles = array(
		// wp-admin
		'wp-admin', 'install', 'wp-color-picker', 'customize-controls', 'customize-widgets', 'customize-nav-menus', 'ie', 'login', 'press-this',
		// wp-includes
		'buttons', 'admin-bar', 'wp-auth-check', 'editor-buttons', 'media-views', 'wp-pointer',
		'wp-jquery-ui-dialog',
		// deprecated
		'media', 'farbtastic',
	);

	foreach ( $rtl_styles as $rtl_style ) {
		$styles->add_data( $rtl_style, 'rtl', 'replace' );
		if ( $suffix ) {
			$styles->add_data( $rtl_style, 'suffix', $suffix );
		}
	}
}

function wp_style_loader_src( $src, $handle ) {
	global $_wp_admin_css_colors;

	if ( 'colors' == $handle ) {
		$color = get_user_option('admin_color');

		if ( empty($color) || !isset($_wp_admin_css_colors[$color]) )
			$color = 'fresh';

		$color = $_wp_admin_css_colors[$color];
		$parsed = parse_url( $src );
		$url = $color->url;

		if ( ! $url ) {
			return false;
		}

		if ( isset($parsed['query']) && $parsed['query'] ) {
			wp_parse_str( $parsed['query'], $qv );
			$url = add_query_arg( $qv, $url );
		}

		return $url;
	}

	return $src;
}

function _print_styles() {
	global $compress_css;

	$wp_styles = wp_styles();

	$zip = $compress_css ? 1 : 0;
	if ( $zip && defined('ENFORCE_GZIP') && ENFORCE_GZIP )
		$zip = 'gzip';

	if ( !empty($wp_styles->concat) ) {
		$dir = $wp_styles->text_direction;
		$ver = $wp_styles->default_version;
		$href = $wp_styles->base_url . "/wp-admin/load-styles.php?c={$zip}&dir={$dir}&load=" . trim($wp_styles->concat, ', ') . '&ver=' . $ver;
		echo "<link rel='stylesheet' href='" . esc_attr($href) . "' type='text/css' media='all' />\n";

		if ( !empty($wp_styles->print_code) ) {
			echo "<style type='text/css'>\n";
			echo $wp_styles->print_code;
			echo "\n</style>\n";
		}
	}

	if ( !empty($wp_styles->print_html) )
		echo $wp_styles->print_html;
}

