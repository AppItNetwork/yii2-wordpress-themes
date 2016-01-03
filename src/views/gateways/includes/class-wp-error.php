<?php

function is_wp_error( $thing ) {
	return ( $thing instanceof WP_Error );
}