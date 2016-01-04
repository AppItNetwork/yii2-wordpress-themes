<?php

function is_ssl() {
	if ( isset($_SERVER['HTTPS']) ) {
		if ( 'on' == strtolower($_SERVER['HTTPS']) )
			return true;
		if ( '1' == $_SERVER['HTTPS'] )
			return true;
	} elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
		return true;
	}
	return false;
}

function wp_allowed_protocols() {
	static $protocols = array();

	if ( empty( $protocols ) ) {
		$protocols = array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp', 'webcal' );

		// $protocols = apply_filters( 'kses_allowed_protocols', $protocols );
	}

	return $protocols;
}

function wp_parse_args( $args, $defaults = '' ) {
	if ( is_object( $args ) )
		$r = get_object_vars( $args );
	elseif ( is_array( $args ) )
		$r =& $args;
	else
		wp_parse_str( $args, $r );

	if ( is_array( $defaults ) )
		return array_merge( $defaults, $r );
	return $r;
}

function maybe_unserialize( $original ) {
	if ( is_serialized( $original ) ) // don't attempt to unserialize data that wasn't serialized going in
		return @unserialize( $original );
	return $original;
}

function is_serialized( $data, $strict = true ) {
	// if it isn't a string, it isn't serialized.
	if ( ! is_string( $data ) ) {
		return false;
	}
	$data = trim( $data );
 	if ( 'N;' == $data ) {
		return true;
	}
	if ( strlen( $data ) < 4 ) {
		return false;
	}
	if ( ':' !== $data[1] ) {
		return false;
	}
	if ( $strict ) {
		$lastc = substr( $data, -1 );
		if ( ';' !== $lastc && '}' !== $lastc ) {
			return false;
		}
	} else {
		$semicolon = strpos( $data, ';' );
		$brace     = strpos( $data, '}' );
		// Either ; or } must exist.
		if ( false === $semicolon && false === $brace )
			return false;
		// But neither must be in the first X characters.
		if ( false !== $semicolon && $semicolon < 3 )
			return false;
		if ( false !== $brace && $brace < 4 )
			return false;
	}
	$token = $data[0];
	switch ( $token ) {
		case 's' :
			if ( $strict ) {
				if ( '"' !== substr( $data, -2, 1 ) ) {
					return false;
				}
			} elseif ( false === strpos( $data, '"' ) ) {
				return false;
			}
			// or else fall through
		case 'a' :
		case 'O' :
			return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
		case 'b' :
		case 'i' :
		case 'd' :
			$end = $strict ? '$' : '';
			return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
	}
	return false;
}

function _canonical_charset( $charset ) {
	if ( 'UTF-8' === $charset || 'utf-8' === $charset || 'utf8' === $charset ||
		'UTF8' === $charset )
		return 'UTF-8';

	if ( 'ISO-8859-1' === $charset || 'iso-8859-1' === $charset ||
		'iso8859-1' === $charset || 'ISO8859-1' === $charset )
		return 'ISO-8859-1';

	return $charset;
}

function _config_wp_siteurl( $url = '' ) {
	if ( defined( 'WP_SITEURL' ) )
		return untrailingslashit( WP_SITEURL );
	return $url;
}

function add_query_arg() {
	$args = func_get_args();
	if ( is_array( $args[0] ) ) {
		if ( count( $args ) < 2 || false === $args[1] )
			$uri = $_SERVER['REQUEST_URI'];
		else
			$uri = $args[1];
	} else {
		if ( count( $args ) < 3 || false === $args[2] )
			$uri = $_SERVER['REQUEST_URI'];
		else
			$uri = $args[2];
	}

	if ( $frag = strstr( $uri, '#' ) )
		$uri = substr( $uri, 0, -strlen( $frag ) );
	else
		$frag = '';

	if ( 0 === stripos( $uri, 'http://' ) ) {
		$protocol = 'http://';
		$uri = substr( $uri, 7 );
	} elseif ( 0 === stripos( $uri, 'https://' ) ) {
		$protocol = 'https://';
		$uri = substr( $uri, 8 );
	} else {
		$protocol = '';
	}

	if ( strpos( $uri, '?' ) !== false ) {
		list( $base, $query ) = explode( '?', $uri, 2 );
		$base .= '?';
	} elseif ( $protocol || strpos( $uri, '=' ) === false ) {
		$base = $uri . '?';
		$query = '';
	} else {
		$base = '';
		$query = $uri;
	}

	wp_parse_str( $query, $qs );
	$qs = urlencode_deep( $qs ); // this re-URL-encodes things that were already in the query string
	if ( is_array( $args[0] ) ) {
		foreach ( $args[0] as $k => $v ) {
			$qs[ $k ] = $v;
		}
	} else {
		$qs[ $args[0] ] = $args[1];
	}

	foreach ( $qs as $k => $v ) {
		if ( $v === false )
			unset( $qs[$k] );
	}

	$ret = build_query( $qs );
	$ret = trim( $ret, '?' );
	$ret = preg_replace( '#=(&|$)#', '$1', $ret );
	$ret = $protocol . $base . $ret . $frag;
	$ret = rtrim( $ret, '?' );
	return $ret;
}

function build_query( $data ) {
	return _http_build_query( $data, null, '&', '', false );
}

function _http_build_query( $data, $prefix = null, $sep = null, $key = '', $urlencode = true ) {
	$ret = array();

	foreach ( (array) $data as $k => $v ) {
		if ( $urlencode)
			$k = urlencode($k);
		if ( is_int($k) && $prefix != null )
			$k = $prefix.$k;
		if ( !empty($key) )
			$k = $key . '%5B' . $k . '%5D';
		if ( $v === null )
			continue;
		elseif ( $v === false )
			$v = '0';

		if ( is_array($v) || is_object($v) )
			array_push($ret,_http_build_query($v, '', $sep, $k, $urlencode));
		elseif ( $urlencode )
			array_push($ret, $k.'='.urlencode($v));
		else
			array_push($ret, $k.'='.$v);
	}

	if ( null === $sep )
		$sep = ini_get('arg_separator.output');

	return implode($sep, $ret);
}

function _doing_it_wrong( $function, $message, $version ) {

	do_action( 'doing_it_wrong_run', $function, $message, $version );

	if ( WP_DEBUG && apply_filters( 'doing_it_wrong_trigger_error', true ) ) {
		if ( function_exists( '__' ) ) {
			$version = is_null( $version ) ? '' : sprintf( __( '(This message was added in version %s.)' ), $version );
			/* translators: %s: Codex URL */
			$message .= ' ' . sprintf( __( 'Please see <a href="%s">Debugging in WordPress</a> for more information.' ),
				__( 'https://codex.wordpress.org/Debugging_in_WordPress' )
			);
			trigger_error( sprintf( __( '%1$s was called <strong>incorrectly</strong>. %2$s %3$s' ), $function, $message, $version ) );
		} else {
			$version = is_null( $version ) ? '' : sprintf( '(This message was added in version %s.)', $version );
			$message .= sprintf( ' Please see <a href="%s">Debugging in WordPress</a> for more information.',
				'https://codex.wordpress.org/Debugging_in_WordPress'
			);
			trigger_error( sprintf( '%1$s was called <strong>incorrectly</strong>. %2$s %3$s', $function, $message, $version ) );
		}
	}
}

function wp_post_preview_js() {
	global $post;

	if ( ! is_preview() || empty( $post ) ) {
		return;
	}

	// Has to match the window name used in post_submit_meta_box()
	$name = 'wp-preview-' . (int) $post->ID;

	?>
	<script>
	( function() {
		var query = document.location.search;

		if ( query && query.indexOf( 'preview=true' ) !== -1 ) {
			window.name = '<?php echo $name; ?>';
		}

		if ( window.addEventListener ) {
			window.addEventListener( 'unload', function() { window.name = ''; }, false );
		}
	}());
	</script>
	<?php
}

function wp_json_encode( $data, $options = 0, $depth = 512 ) {
	/*
	 * json_encode() has had extra params added over the years.
	 * $options was added in 5.3, and $depth in 5.5.
	 * We need to make sure we call it with the correct arguments.
	 */
	if ( version_compare( PHP_VERSION, '5.5', '>=' ) ) {
		$args = array( $data, $options, $depth );
	} elseif ( version_compare( PHP_VERSION, '5.3', '>=' ) ) {
		$args = array( $data, $options );
	} else {
		$args = array( $data );
	}

	// Prepare the data for JSON serialization.
	$data = _wp_json_prepare_data( $data );

	$json = @call_user_func_array( 'json_encode', $args );

	// If json_encode() was successful, no need to do more sanity checking.
	// ... unless we're in an old version of PHP, and json_encode() returned
	// a string containing 'null'. Then we need to do more sanity checking.
	if ( false !== $json && ( version_compare( PHP_VERSION, '5.5', '>=' ) || false === strpos( $json, 'null' ) ) )  {
		return $json;
	}

	try {
		$args[0] = _wp_json_sanity_check( $data, $depth );
	} catch ( Exception $e ) {
		return false;
	}

	return call_user_func_array( 'json_encode', $args );
}

function _wp_json_prepare_data( $data ) {
	if ( ! defined( 'WP_JSON_SERIALIZE_COMPATIBLE' ) || WP_JSON_SERIALIZE_COMPATIBLE === false ) {
		return $data;
	}

	switch ( gettype( $data ) ) {
		case 'boolean':
		case 'integer':
		case 'double':
		case 'string':
		case 'NULL':
			// These values can be passed through.
			return $data;

		case 'array':
			// Arrays must be mapped in case they also return objects.
			return array_map( '_wp_json_prepare_data', $data );

		case 'object':
			// If this is an incomplete object (__PHP_Incomplete_Class), bail.
			if ( ! is_object( $data ) ) {
				return null;
			}

			if ( $data instanceof JsonSerializable ) {
				$data = $data->jsonSerialize();
			} else {
				$data = get_object_vars( $data );
			}

			// Now, pass the array (or whatever was returned from jsonSerialize through).
			return _wp_json_prepare_data( $data );

		default:
			return null;
	}
}

function absint( $maybeint ) {
	return abs( intval( $maybeint ) );
}

function _config_wp_home( $url = '' ) {
	if ( defined( 'WP_HOME' ) )
		return untrailingslashit( WP_HOME );
	return $url;
}

function force_ssl_admin( $force = null ) {
	static $forced = false;

	if ( !is_null( $force ) ) {
		$old_forced = $forced;
		$forced = $force;
		return $old_forced;
	}

	return $forced;
}

function mbstring_binary_safe_encoding( $reset = false ) {
	static $encodings = array();
	static $overloaded = null;

	if ( is_null( $overloaded ) )
		$overloaded = function_exists( 'mb_internal_encoding' ) && ( ini_get( 'mbstring.func_overload' ) & 2 );

	if ( false === $overloaded )
		return;

	if ( ! $reset ) {
		$encoding = mb_internal_encoding();
		array_push( $encodings, $encoding );
		mb_internal_encoding( 'ISO-8859-1' );
	}

	if ( $reset && $encodings ) {
		$encoding = array_pop( $encodings );
		mb_internal_encoding( $encoding );
	}
}

function reset_mbstring_encoding() {
	mbstring_binary_safe_encoding( true );
}

function wp_filter_object_list( $list, $args = array(), $operator = 'and', $field = false ) {
	if ( ! is_array( $list ) )
		return array();

	$list = wp_list_filter( $list, $args, $operator );

	if ( $field )
		$list = wp_list_pluck( $list, $field );

	return $list;
}

function wp_list_filter( $list, $args = array(), $operator = 'AND' ) {
	if ( ! is_array( $list ) )
		return array();

	if ( empty( $args ) )
		return $list;

	$operator = strtoupper( $operator );
	$count = count( $args );
	$filtered = array();

	foreach ( $list as $key => $obj ) {
		$to_match = (array) $obj;

		$matched = 0;
		foreach ( $args as $m_key => $m_value ) {
			if ( array_key_exists( $m_key, $to_match ) && $m_value == $to_match[ $m_key ] )
				$matched++;
		}

		if ( ( 'AND' == $operator && $matched == $count )
		  || ( 'OR' == $operator && $matched > 0 )
		  || ( 'NOT' == $operator && 0 == $matched ) ) {
			$filtered[$key] = $obj;
		}
	}

	return $filtered;
}

function validate_file( $file, $allowed_files = '' ) {
	if ( false !== strpos( $file, '..' ) )
		return 1;

	if ( false !== strpos( $file, './' ) )
		return 1;

	if ( ! empty( $allowed_files ) && ! in_array( $file, $allowed_files ) )
		return 3;

	if (':' == substr( $file, 1, 1 ) )
		return 2;

	return 0;
}

function wp_maybe_load_widgets() {
	if ( ! apply_filters( 'load_default_widgets', true ) ) {
		return;
	}

	require_once( ABSPATH . WPINC . '/default-widgets.php' );

	add_action( '_admin_menu', 'wp_widgets_add_menu' );
}

function add_magic_quotes( $array ) {
	foreach ( (array) $array as $k => $v ) {
		if ( is_array( $v ) ) {
			$array[$k] = add_magic_quotes( $v );
		} else {
			$array[$k] = addslashes( $v );
		}
	}
	return $array;
}

function is_blog_installed() { return true; }

function smilies_init() {
	global $wpsmiliestrans, $wp_smiliessearch;

	// don't bother setting up smilies if they are disabled
	if ( !get_option( 'use_smilies' ) )
		return;

	if ( !isset( $wpsmiliestrans ) ) {
		$wpsmiliestrans = array(
		':mrgreen:' => 'mrgreen.png',
		':neutral:' => "\xf0\x9f\x98\x90",
		':twisted:' => "\xf0\x9f\x98\x88",
		  ':arrow:' => "\xe2\x9e\xa1",
		  ':shock:' => "\xf0\x9f\x98\xaf",
		  ':smile:' => 'simple-smile.png',
		    ':???:' => "\xf0\x9f\x98\x95",
		   ':cool:' => "\xf0\x9f\x98\x8e",
		   ':evil:' => "\xf0\x9f\x91\xbf",
		   ':grin:' => "\xf0\x9f\x98\x80",
		   ':idea:' => "\xf0\x9f\x92\xa1",
		   ':oops:' => "\xf0\x9f\x98\xb3",
		   ':razz:' => "\xf0\x9f\x98\x9b",
		   ':roll:' => 'rolleyes.png',
		   ':wink:' => "\xf0\x9f\x98\x89",
		    ':cry:' => "\xf0\x9f\x98\xa5",
		    ':eek:' => "\xf0\x9f\x98\xae",
		    ':lol:' => "\xf0\x9f\x98\x86",
		    ':mad:' => "\xf0\x9f\x98\xa1",
		    ':sad:' => 'frownie.png',
		      '8-)' => "\xf0\x9f\x98\x8e",
		      '8-O' => "\xf0\x9f\x98\xaf",
		      ':-(' => 'frownie.png',
		      ':-)' => 'simple-smile.png',
		      ':-?' => "\xf0\x9f\x98\x95",
		      ':-D' => "\xf0\x9f\x98\x80",
		      ':-P' => "\xf0\x9f\x98\x9b",
		      ':-o' => "\xf0\x9f\x98\xae",
		      ':-x' => "\xf0\x9f\x98\xa1",
		      ':-|' => "\xf0\x9f\x98\x90",
		      ';-)' => "\xf0\x9f\x98\x89",
		// This one transformation breaks regular text with frequency.
		//     '8)' => "\xf0\x9f\x98\x8e",
		       '8O' => "\xf0\x9f\x98\xaf",
		       ':(' => 'frownie.png',
		       ':)' => 'simple-smile.png',
		       ':?' => "\xf0\x9f\x98\x95",
		       ':D' => "\xf0\x9f\x98\x80",
		       ':P' => "\xf0\x9f\x98\x9b",
		       ':o' => "\xf0\x9f\x98\xae",
		       ':x' => "\xf0\x9f\x98\xa1",
		       ':|' => "\xf0\x9f\x98\x90",
		       ';)' => "\xf0\x9f\x98\x89",
		      ':!:' => "\xe2\x9d\x97",
		      ':?:' => "\xe2\x9d\x93",
		);
	}

	if (count($wpsmiliestrans) == 0) {
		return;
	}

	/*
	 * NOTE: we sort the smilies in reverse key order. This is to make sure
	 * we match the longest possible smilie (:???: vs :?) as the regular
	 * expression used below is first-match
	 */
	krsort($wpsmiliestrans);

	$spaces = wp_spaces_regexp();

	// Begin first "subpattern"
	$wp_smiliessearch = '/(?<=' . $spaces . '|^)';

	$subchar = '';
	foreach ( (array) $wpsmiliestrans as $smiley => $img ) {
		$firstchar = substr($smiley, 0, 1);
		$rest = substr($smiley, 1);

		// new subpattern?
		if ($firstchar != $subchar) {
			if ($subchar != '') {
				$wp_smiliessearch .= ')(?=' . $spaces . '|$)';  // End previous "subpattern"
				$wp_smiliessearch .= '|(?<=' . $spaces . '|^)'; // Begin another "subpattern"
			}
			$subchar = $firstchar;
			$wp_smiliessearch .= preg_quote($firstchar, '/') . '(?:';
		} else {
			$wp_smiliessearch .= '|';
		}
		$wp_smiliessearch .= preg_quote($rest, '/');
	}

	$wp_smiliessearch .= ')(?=' . $spaces . '|$)/m';

}
