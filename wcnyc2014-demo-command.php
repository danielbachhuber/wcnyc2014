<?php
/**
 * Demonstration command for WordCamp NYC 2014
 */
class WCNYC2014_Demo_Command extends WP_CLI_Command {

	/**
	 * Iterators
	 */
	public function iterators() {

	}

	/**
	 * PHP-CLI-Tools
	 *
	 * @
	 */
	public function cli_tools() {

	}


}

WP_CLI::add_command( 'wcnyc2014 demo', 'WCNYC2014_Demo_Command' );