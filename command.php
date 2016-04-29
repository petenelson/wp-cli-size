<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

define( 'WP_CLI_SIZE_ROOT', dirname( __FILE__ ) );

require_once WP_CLI_SIZE_ROOT . '/includes/class-wp-cli-size-base-command.php';
require_once WP_CLI_SIZE_ROOT . '/includes/class-wp-cli-size-command.php';
