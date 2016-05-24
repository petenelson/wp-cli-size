<?php

/**
 * Gets database and table sizes
 */
class WP_CLI_Size_Command extends WP_CLI_Size_Base_Command  {


	/**
	 * Gets the WordPress database size
	 *
	 * ## OPTIONS
	 *
	 * [<database>]
	 * : Database name(s), defaults to the current WordPress database
	 *
	 * [--format]
	 * : table, csv, json
	 * 
	 * ## EXAMPLES
	 *
	 *     wp size database
	 *
	 *     wp size database wp_default wp_mysite --format=csv
	 *
	 * @subcommand database
	 *
	 * @synopsis [<database>...] [--format]
	 */
	function database( $positional_args, $assoc_args = array() ) {

		$format         = ! empty( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';
		$database_names = empty( $positional_args ) ? array( DB_NAME ) : $positional_args;

		$sizes = array();
		foreach( $database_names as $database_name ) {
			$sizes[] = $this->size_to_row( $this->get_database_size( $database_name ) );
		}

		$args = array( 'format' => $format );

		$formatter = new \WP_CLI\Formatter(
			$args,
			$this->fields()
		);

		$formatter->display_items( $sizes );

	}


	/**
	 * Gets the WordPress table sizes
	 *
	 * ## OPTIONS
	 *
	 * [<table>]
	 * : List of table names, defaults to all tables in the current site
	 *
	 * [--format]
	 * : table, csv, json
	 *
	 * [--network]
	 * : List all the tables registered to $wpdb in a multisite install.
	 *
	 * [--all-tables-with-prefix]
	 * : List any tables that match the table prefix even if not registered
	 * on $wpdb.
	 *
	 * [--all-tables]
	 * : List ALL tables in the database, regardless of the prefix, and
	 * even if not registered on $wpdb. Overrides --network and
	 * --all-tables-with-prefix.
	 * 
	 * ## EXAMPLES
	 *
	 *     wp size tables
	 *
	 * @subcommand tables
	 *
	 * @synopsis [<table>...] [--format] [--network] [--all-tables-with-prefix] [--all-tables]
	 */
	function tables( $positional_args, $assoc_args = array() ) {

		global $wpdb;

		$table_names = WP_CLI\Utils\wp_get_table_names( $positional_args, $assoc_args );

		$format = ! empty( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';
		$database_name = $wpdb->dbname;

		$sizes = array();
		foreach ( $table_names as $table_name ) {
			 $size = $this->get_table_size( $database_name, $table_name );
			 $size['Rows'] = $this->get_row_count( $database_name, $table_name );
			 $sizes[] = $size;
		}

		$args = array( 'format' => $format );

		$fields = $this->fields();
		$fields[] = 'Rows';

		$formatter = new \WP_CLI\Formatter(
			$args,
			$fields
		);

		$formatter->display_items( $sizes );

	}


	/**
	 * Gets the WordPress upload folder size
	 *
	 * ## OPTIONS
	 *
	 * [--format]
	 * : table, csv, json
	 *
	 * [--network]
	 * : gets the size of all upload directories in a multisite install
	 *
	 * ## EXAMPLES
	 *
	 *     wp size media
	 *
	 *     wp size media --network
	 *
	 * @subcommand media
	 *
	 * @synopsis [--format] [--network]
	 */
	function media( $positional_args, $assoc_args = array() ) {

		$format = ! empty( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';
		$network = ! empty( $assoc_args['network'] );

		$fields = $this->fields();
		$sizes = array();

		if ( $network && is_multisite() ) {
			$sites = wp_get_sites();

			foreach ( $sites as $site ) {
				switch_to_blog( $site['blog_id'] );

				$upload_dir = wp_upload_dir();
				if ( ! empty( $upload_dir ) ) {
					$size = $this->size_to_row( array(
						'name' => $upload_dir['basedir'],
						'size' => $this->get_directory_size( $upload_dir['basedir'] ),
							) );
					$sizes[] = $size;
				}

			}

		} else {

			$upload_dir = wp_upload_dir();
			if ( ! empty( $upload_dir ) ) {
				$size = $this->size_to_row( array(
					'name' => $upload_dir['basedir'],
					'size' => $this->get_directory_size( $upload_dir['basedir'] ),
						) );
				$sizes[] = $size;
			}

		}




		$formatter = new \WP_CLI\Formatter(
			$args,
			$fields
		);

		$formatter->display_items( $sizes );

	}


	private function fields() {
		return array( 'Name', 'Size', 'Bytes' );
	}


	private function size_to_row( $size_array ) {
		return array(
			'Name'  => $size_array['name'],
			'Size'  => size_format( $size_array['size'] ),
			'Bytes' => $size_array['size'],
			);
	}


	private function get_database_size( $database_name = '' ) {

		global $wpdb;

		$database = array();
		$database['name'] = $database_name;
		$database['size'] = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(data_length + index_length) FROM information_schema.TABLES where table_schema = '%s' GROUP BY table_schema;", $database_name ) );

		return $database;
	}


	private function get_table_size( $database_name, $table_name ) {

		global $wpdb;

		$size = $wpdb->get_row( $wpdb->prepare(
			"SELECT Table_Name as Name, SUM(data_length + index_length) as Bytes FROM information_schema.TABLES where table_schema = '%s' and Table_Name = '%s' GROUP BY Table_Name LIMIT 1",
			$database_name,
			$table_name
			),
		ARRAY_A
		);

		if ( ! empty( $size ) ) {
			$size['Size'] = size_format( $size['Bytes'] );
		}

		return $size;
	}


	private function get_row_count( $database_name, $table_name ) {
		global $wpdb;
		$database_name = sanitize_key( $database_name );
		$table_name   = sanitize_key( $table_name );
		return $wpdb->get_var( "SELECT count(*) FROM `{$database_name}`.`{$table_name}`" );
	}


	private function get_directory_size( $path ) {

		require_once ABSPATH . 'wp-includes/ms-functions.php';

		if ( ! is_dir( $path ) ) {
			$size = -1;
		} else {
			$size = recurse_dirsize( $path );
		}

		return $size;

	}


}

WP_CLI::add_command( 'size', 'WP_CLI_Size_Command' );
