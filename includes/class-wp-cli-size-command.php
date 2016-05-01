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
	 * [<database_name>]
	 * Ddatabase name(s), defaults to the current WordPress database
	 *
	 * --format
	 * table, csv, json
	 * 
	 * ## EXAMPLES
	 *
	 *     wp size database
	 *
	 *     wp size database wp_default wp_mysite --format=csv
	 *
	 * @subcommand database
	 *
	 * @synopsis [<database_name>...] [--format]
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
	 * [<table_name>]
	 * List of table names, defaults to all tables
	 *
	 * --database
	 * Database name, defaults to current WordPress database
	 *
	 * --format
	 * table, csv, json
	 * 
	 * ## EXAMPLES
	 *
	 *     wp size tables
	 *
	 * @subcommand tables
	 *
	 * @synopsis [<table_name>...] [--database] [--format]
	 */
	function tables( $positional_args, $assoc_args = array() ) {

		$format = ! empty( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';
		$database_name = ! empty( $assoc_args['database'] ) ? $assoc_args['database'] : DB_NAME;

		$tables = empty( $positional_args ) ? $this->get_table_list( $database_name ) : $positional_args;
		$sizes = array();
		foreach ( $tables as $table_name ) {
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


	private function get_table_list( $database_name ) {

		global $wpdb;
		return $wpdb->get_col( $wpdb->prepare( "SELECT Table_Name as Name FROM information_schema.TABLES where table_schema = '%s'", $database_name ) );
	}


	private function get_row_count( $database_name, $table_name ) {
		global $wpdb;
		$database_name = sanitize_key( $database_name );
		$table_name   = sanitize_key( $table_name );
		return $wpdb->get_var( "SELECT count(*) FROM `{$database_name}`.`{$table_name}`" );
	}

}

WP_CLI::add_command( 'size', 'WP_CLI_Size_Command' );
