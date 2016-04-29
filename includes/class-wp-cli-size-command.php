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
	 * --format
	 * table, csv, json
	 * 
	 * ## EXAMPLES
	 *
	 *     wp size database
	 *
	 * @subcommand database
	 *
	 * @synopsis [--format]
	 */
	function database( $positional_args, $assoc_args = array() ) {

		$format        = ! empty( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';

		$database_name = DB_NAME;
		$database_size = $this->get_database_size( $database_name );

		$args = array( 'format' => $format );

		$formatter = new \WP_CLI\Formatter(
			$args,
			$this->fields()
		);

		$formatter->display_items( array( $this->size_to_row( $database_size ) ) );

	}


	function tables() {

		$format = ! empty( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';

		$database_name = DB_NAME;

		$tables = $this->get_table_list( $database_name );
		$sizes = array();
		foreach ( $tables as $table_name ) {
			$sizes[] = $this->get_table_size( $database_name, $table_name );
		}


		$args = array( 'format' => $format );

		$formatter = new \WP_CLI\Formatter(
			$args,
			$this->fields()
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

	private function get_database_size( $db_name = '' ) {

		global $wpdb;

		$database = array();
		$database['name'] = $db_name;
		$database['size'] = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(data_length + index_length) FROM information_schema.TABLES where table_schema = '%s' GROUP BY table_schema;", DB_NAME ) );

		return $database;
	}


	private function get_table_size( $db_name, $table_name ) {

		global $wpdb;

		$size = $wpdb->get_row( $wpdb->prepare(
			"SELECT Table_Name as Name, SUM(data_length + index_length) as Bytes FROM information_schema.TABLES where table_schema = '%s' and Table_Name = '%s' GROUP BY Table_Name LIMIT 1",
			$db_name,
			$table_name
			),
		ARRAY_A
		);

		if ( ! empty( $size ) ) {
			$size['Size'] = size_format( $size['Bytes'] );
		}

		return $size;
	}


	private function get_table_list( $db_name ) {

		global $wpdb;

		return $wpdb->get_col( $wpdb->prepare( "SELECT Table_Name as Name FROM information_schema.TABLES where table_schema = '%s'", $db_name ) );
	}

}

WP_CLI::add_command( 'size', 'WP_CLI_Size_Command' );
