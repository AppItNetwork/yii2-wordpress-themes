<?php
use appitnetwork\wpthemes\helpers\WP_Locale;

function is_rtl() {
	global $wp_locale;
	return $wp_locale->is_rtl();
}
